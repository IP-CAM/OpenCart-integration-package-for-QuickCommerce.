<?php
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;

use App\Resource\Product;
use App\Resource\Invoice;
use App\Resource\InvoiceLine;
//use App\Resource\Language;
use App\Resource\Option;
use App\Resource\ProductOption;
use App\Resource\ProductOptionValue;

use Ddeboer\DataImport\Writer\DoctrineCallbackWriter;
use Doctrine\Common\Util\Debug;

// At a minimum, invoices and cash sales must be supported
/**
 * Class TransactionWorkOrder
 */
class TransactionWorkOrder extends QcResource {
	protected $className = 'OcWorkOrder';
	
	protected $tMeta;
	protected $iMeta;
	protected $ilMeta;
	protected $ooMeta;
	protected $cMeta;
	protected $sMeta;
	
	private function loadMetadata() {
		$this->tMeta = $this->context->em->getClassMetadata('OcTransaction');
		$this->iMeta = $this->context->em->getClassMetadata('OcInvoice');
		$this->ilMeta = $this->context->em->getClassMetadata('OcInvoiceLine');
		$this->ooMeta = $this->context->em->getClassMetadata('OcOrderOption'); // TODO: Check if there's a quantity?
		$this->cMeta = $this->context->em->getClassMetadata('OcCustomer');
		$this->sMeta = $this->context->em->getClassMetadata('OcStore');
	}
    
    public function search($params = array(), $serialize = true, $tableize = true) {
        $iService = new App\Resource\Invoice($this->context->em, 'OcWorkOrder');
        return $iService->search($params, $serialize, $tableize);
    }
	
	/**
	 * Converts an order to an invoice
	 * TODO: Inject Order entity?
	 */
	public function __convert($id) {		
		// TODO: Inject this so we don't reload the model every single time
		$this->context->load->model('sale/order');
		//$this->context->load->model('localisation/tax_class');
		//$this->context->load->model('localisation/stock_status');
		$this->context->load->model('customer/customer');
		$this->context->load->model('customer/customer_group');
		$this->context->load->model('setting/store');
		
		// Load the order and any associated data
		$o = $this->context->model_sale_order->getOrder($id);
		$products = $this->context->model_sale_order->getOrderProducts($o['order_id']); // Convert to lines
		$customer = $this->context->model_customer_customer->getCustomer($o['customer_id']);
		$group = $this->context->model_customer_customer_group->getCustomerGroup($o['customer_group_id']);
		$store = $this->context->model_setting_store->getStore($o['store_id']);
		
		$order = $this->context->em->find('OcOrder', $id);
		
		//$tax = $this->context->model_localisation_tax_class->getTaxClass($o['tax_class_id']);
		//$stock = $this->context->model_localisation_stock_status->getStockStatus($o['stock_status_id']);
		
		// Map the invoice
		$i = ObjectFactory::createEntity($this->context->em, 'OcInvoice', array(), array('customer' => $customer, 'customerGroup' => $group));
		
		// Map transaction data
		$t = ObjectFactory::createEntity($this->context->em, 'OcTransaction', array(), array('store' => $store));
		// TODO: URGENT - there are no date added/modified properties on the returned object!
		
		// Copy transaction fields from the order
		foreach ($o as $key => $value) {
			if (array_key_exists($key, $t)) {
				$t[$key] =  $value;
			} elseif ($key === 'order_id') {
				$t['oc_entity_id'] = $o['order_id'];
			} elseif ($key == 'currency_id' || $key == 'store_id') {
				// TODO: I don't think these keys are showing up because they're association keys
				// Set it manually and deal with it later
				$t[$key] =  $value;
			}
		}
		
		// Set the date - we're converting
		$date = new DateTime();
		$t['date_added'] = $date->format('Y-m-d H:i:s');
		$t['date_modified'] = $date->format('Y-m-d H:i:s');
		
		// TODO: I think the transaction ID is being auto-populated because it's set to auto-increment
		$t['oc_entity_id'] = $o['order_id']; // Not sure if that will affect anything but we'll find out soon
		
		$i['oc_entity_id'] = $o['order_id'];
		$i['invoice_no'] = $o['invoice_no'];
		$i['invoice_prefix'] = $o['invoice_prefix'];
		$i['bill_email'] = $o['email'];
		$i['bill_telephone'] = $o['telephone'];
		$i['bill_fax'] = $o['fax'];
		$i['firstname'] = $o['firstname'];
		$i['lastname'] = $o['lastname'];
		$i['bill_addr_firstname'] = $o['payment_firstname'];
		$i['bill_addr_lastname'] = $o['payment_lastname'];
		$i['bill_addr_company'] = $o['payment_company'];
		$i['bill_addr_line1'] = $o['payment_address_1'];
		$i['bill_addr_line2'] = $o['payment_address_2'];
		$i['bill_addr_city'] = $o['payment_city'];
		$i['bill_addr_postcode'] = $o['payment_postcode'];
		$i['bill_addr_country'] = $o['payment_country'];
		$i['bill_addr_country_id'] = $o['payment_country_id'];
		$i['bill_addr_zone'] = $o['payment_zone'];
		$i['bill_addr_zone_id'] = $o['payment_zone_id'];
		$i['ship_addr_firstname'] = $o['shipping_firstname'];
		$i['ship_addr_lastname'] = $o['shipping_lastname'];
		$i['ship_addr_company'] = $o['shipping_company'];
		$i['ship_addr_line_1'] = $o['shipping_address_1'];
		$i['ship_addr_line_2'] = $o['shipping_address_2'];
		$i['ship_addr_city'] = $o['shipping_city'];
		$i['ship_addr_postcode'] = $o['shipping_postcode'];
		$i['ship_addr_country'] = $o['shipping_country'];
		$i['ship_addr_country_id'] = $o['shipping_country_id'];
		$i['ship_addr_zone'] = $o['shipping_zone'];
		$i['ship_addr_zone_id'] = $o['shipping_zone_id'];
		$i['total'] = $o['total'];
		//$i['balance'] = $o['balance']; // QBO read-only?
		//$i['deposit'] = $o['deposit']; // QBO read-only?
		//$i['tracking_no'] = $o['tracking'];
		$i['payment_method'] = $o['payment_method'];
		$i['shipping_method'] = $o['shipping_method'];
		
		$lines = [];
		
		foreach ($products as $p) {
			$l = ObjectFactory::createEntity($this->context->em, 'OcInvoiceLine', $p);
			// TODO: Fix mappings
			$l['product_id'] = $p['product_id'];
			
			$lines[] = $l;
			//var_dump($p);
		}
		
		$i['lines'] = $lines;
		$t['invoice'] = $i;
		
		$this->addTransaction($t);
	}

	/**
	 * Creates the base transaction
	 *
	 * @param null $model
	 * @return mixed
	 */
	public function addTransaction($model) {
		// TODO: Real transactions and rollback!!! These actions are atomic right now
		$mappings = [];
		$export = false; // Saves a step later

		$this->loadMetadata();

		$this->mapTransaction($mappings);

		// Create the transaction
		// I could do this via DoctrineWriter, but I don't want to create a total dependency
		// I want to use DoctrineWriter for batch operations but not for atomic stuff
		$entity = new OcTransaction();
		$converter = new MappingItemConverter();

		foreach ($mappings as $key => $value) {
			$to = $key;
			$from = $value;

			if (is_string($from)) {
				$converter->addMapping($from, $to); // TODO: The mappings need to be reversed for this...
			} elseif (is_array($from) && (array_keys($from) !== range(0, count($from) - 1))) {
				// Only process associative arrays, we need to use a different converter for sequential arrays
				$converter->addMapping($to, array_flip($value));
			}
		}

		$output = []; // For testing
		$testWriter = new ArrayWriter($output);

		// Get the root node
		$tModel = array_map(function ($item) {
			return (is_scalar($item)) ? $item : null;
		}, $model);

		// TODO: Delete extra assoc keys? (store_id, currency_id)
		// Dump $tModel to see what I mean
		/*foreach ($tModel as $key => $value) {
			$tModel[Inflector::camelize($key)] = $value;
		}*/

		$dtEntity = false; // Doctrine OcTransaction entity

		$reader = new ArrayReader(array($tModel));
		//$writer = new DoctrineWriter($this->context->em, 'OcTransaction');
		$writer = new DoctrineCallbackWriter($this->context->em, 'OcTransaction',
			function ($entity, $writer) use (&$dtEntity) {
				$dtEntity = $entity;
			});

		$writer->setBatchSize(1); // Atomic update
		$writer->disableTruncate();

		$workflow = new Workflow($reader);
		$workflow->addItemConverter($converter);

		$dateConverter = new DateTimeValueConverter();
		$workflow->addValueConverter('dateAdded', $dateConverter);
		$workflow->addValueConverter('dateModified', $dateConverter);

		$workflow->addWriter($writer);
		$workflow->process();

		$transactionId = false;

		// This is a core table so we don't need to bother making the pk configurable
		if ($dtEntity) {
			$transactionId = $dtEntity->getTransactionId();
		}

		$mappings = []; // Clear mappings for var reuse

		// I left a note in the mapDoctrineEntity method regarding nesting
		$this->mapWorkOrder($mappings);

		// TODO: If transaction id is false we need to throw an error or exception
		$model = $model['invoice']; // TODO: Detect type and switch array key accordingly
		$model['transaction_id'] = $transactionId;
		$model['transaction'] = $transactionId;

		// Strip out lines and process separately
		// Until I implement a DoctrineCallbackWriter class, I can't process all at once
		// Just follow this block of code...
		$tLines = ($model['lines'] != null) ? $model['lines'] : array();
		//var_dump($tLines);
		unset($model['lines']);

		$reader = new ArrayReader(array($model));
		// TODO: Throw error if linked transaction (eg: invoice) not set
		$workflow = new Workflow($reader);
		$output = [];

		// TODO: DoctrineCallbackWriter
		//$writer = new DoctrineWriter($this->context->em, 'OcWorkOrder');
		$writer = new DoctrineCallbackWriter($this->context->em, 'OcInvoice',
			//function ($entity, $writer) {} );
			function ($entity, $writer) use (&$dtEntity, $mappings, $tLines) {
				// Test lazy loading
				//var_dump($entity->getTransaction()->getStoreName());
				//var_dump($entity->getCustomer()->getEmail());

				$lines = $entity->getLines();

				$reader = new ArrayReader($tLines);
				$lineWriter = new DoctrineCallbackWriter($this->context->em, 'OcInvoiceLine',
					function ($lineEntity, $item, $writer) use (&$entity, &$dtEntity) {
						// TODO: Not sure why I'm still having to set these props... something is wrong with the mappings
						// Let's just get this working for now
						$lineEntity->setInvoice($entity);
						$lineEntity->setProduct($this->getEntityManager()->getRepository('OcProduct')->find($item['product_id']));
						$lineEntity->setOrderProductId($item['order_product_id']);
						$lineEntity->setOrderId($item['order_id']);
						$lineEntity->setDetailType('SalesItemLineDetail');
						//$lineEntity->setDetailType('InvoiceLineItem'); // TODO: This isn't mapped or in Doctrine model
					});

				$lineWriter->disableTruncate();

				$workflow = new Workflow($reader);
				$workflow->addWriter($lineWriter);

				$converter = new MappingItemConverter();
				foreach ($mappings['lines'][0] as $key => $value) {
					$to = $key;
					$from = $value;

					if (is_string($from)) {
						$converter->addMapping($from, $to); // TODO: The mappings need to be reversed for this...
					} elseif (is_array($from) && (array_keys($from) !== range(0, count($from) - 1))) {
						// Only process associative arrays, we need to use a different converter for sequential arrays
						$converter->addMapping($to, array_flip($value));
					} elseif (is_array($from) && (array_keys($from) == range(0, count($from) - 1))) {
						//$nestedConverter->addMapping($to, array_flip($from[0]));
					}
				}

				$workflow->addItemConverter($converter);
				//var_dump($converter);
				$workflow->process();
			});
		//$writer = new ArrayWriter($output);

		$writer->disableTruncate();

		$workflow->addWriter($writer);

		$converter = new MappingItemConverter();
		//$nestedConverter = new NestedMappingItemConverter('lines');

		foreach ($mappings as $key => $value) {
			$to = $key;
			$from = $value;

			if (is_string($from)) {
				$converter->addMapping($from, $to); // TODO: The mappings need to be reversed for this...
			} elseif (is_array($from) && (array_keys($from) !== range(0, count($from) - 1))) {
				// Only process associative arrays, we need to use a different converter for sequential arrays
				$converter->addMapping($to, array_flip($value));
			} elseif (is_array($from) && (array_keys($from) == range(0, count($from) - 1))) {
				$from[0]['orderProductId'] = 'order_product_id'; // TODO: Mappings aren't working right here!!!
				$from[0]['orderId'] = 'order_product_id'; // TODO: Mappings aren't working right here!!!

				//$nestedConverter->addMapping($to, array_flip($from[0]));
			}
		}

		$workflow->addItemConverter($converter);
		//$workflow->addItemConverter($nestedConverter);
		$workflow->process();
	}

	protected function setValue($entity, $value, $setter) {
		if (method_exists($entity, $setter)) {
			$entity->$setter($value);
		}
	}

	protected function setData(array $item, &$entity) {
		$fieldNames = array_merge($this->iMeta->getFieldNames(), $this->iMeta->getAssociationNames());
		foreach ($fieldNames as $fieldName) {

			$value = null;
			if (isset($item[$fieldName])) {
				$value = $item[$fieldName];
			} elseif (method_exists($item, 'get' . ucfirst($fieldName))) {
				$value = $item->{'get' . ucfirst($fieldName)};
			}

			if (null === $value) {
				continue;
			}

			// TODO: Need to move this patch outta here in case I refresh the lib
			if (!($value instanceof \DateTime)
				|| $value != $this->iMeta->getFieldValue($entity, $fieldName)
			) {
				// Looks like this was done for Doctrine 1.x, it's not working in 2.x (methods like from/toArray were removed... might have something to do with it?
				/*if ($this->iMeta->hasAssociation($fieldName)) {
					// Don't set assoc, it won't work!
					// I need some better checks in here... I might have my mappings incorrectly specified but I'm not getting a relationship type in the association meta
					$associationMapping = $this->iMeta->getAssociationMapping($fieldName);

					if (is_array($value) && (array_keys($value) == range(0, count($value) - 1))) {
					} else {
						$value = $this->getEntityManager()->getReference($associationMapping['targetEntity'], $value);

						$setter = 'set' . ucfirst($fieldName);
						$this->setValue($entity, $value, $setter);
					}
				} else {
					$setter = 'set' . ucfirst($fieldName);
					$this->setValue($entity, $value, $setter);
				}*/
				
				// Set associations by hand or this is gonna get retarded

				//$setter = 'set' . ucfirst($fieldName);
				//$this->setValue($entity, $value, $setter);
			}
		}
	}

	/**
	 * Creates the base transaction
	 *
	 * @param null $model
	 * @return mixed
	 */
	public function editTransaction($model) {
		$this->loadMetadata();

		$i = $this->getEntity($model['invoice']['invoice_id'], false);

		// Just something to trigger transaction load
		$tService = new Transaction($this->context, 'OcTransaction');
		$t = $i->getTransaction();
		$t->getStoreId(); // Trigger lazy load or this blows up... I should probably get rid of lazy loading on this entity
		
		$mappings = array();		
		
		// I left a note in the mapDoctrineEntity method regarding nesting
		// $mappings, $config, $children, $remote
		// Children and map to local
		$this->context->mapDoctrineEntity($mappings, array(
			'OcTransaction' => array(
				'meta' => $this->tMeta
			)
		), true, false);
		
		$this->mapTransaction($mappings); // TODO: Should be just this
		
		// Create the transaction
		$entity = new OcTransaction();
		$converter = new MappingItemConverter();

		foreach ($mappings as $key => $value) {
			$to = $key;
			$from = $value;

			if (is_string($from)) {
				$converter->addMapping($from, $to);
			} elseif (is_array($from) && (array_keys($from) !== range(0, count($from) - 1))) {
				// Only process associative arrays, we need to use a different converter for sequential arrays
				$converter->addMapping($to, array_flip($value));
			}
		}

		// Get the root node
		$tModel = array_map(function ($item) {
			return (is_scalar($item)) ? $item : null;
		}, $model);

		// TODO: Delete extra assoc keys? (store_id, currency_id)
		// Dump $tModel to see what I mean
		/*foreach ($tModel as $key => $value) {
			$tModel[Inflector::camelize($key)] = $value;
		}*/
		
		
		$reader = new ArrayReader(array($tModel));
		//$writer = new DoctrineWriter($this->context->em, 'OcTransaction');
		$writer = new CallbackWriter(
			function ($item) use (&$t, &$tService) {
				try {
					unset($item['dateAdded']);
					unset($item['dateModified']);
					
					$t = $tService->writeItem($item);
					$t->setStoreName('testing');
					$tService->updateEntity($t);
				} catch (Exception $e) {
					throw $e;
				}
			});
		
		$workflow = new Workflow($reader);
		$workflow->addItemConverter($converter);
		self::addDateConverters($workflow);
		$workflow->addWriter($writer);
		$workflow->process();

		$mappings = []; // Clear mappings for var reuse

		// I left a note in the mapDoctrineEntity method regarding nesting
		$this->mapWorkOrder($mappings);

		// TODO: If transaction id is false we need to throw an error or exception
		$model = $model['invoice']; // TODO: Detect type and switch array key accordingly

		// Strip out lines and process separately
		$lines = $model['lines'];
		unset($model['lines']);
		
		$output = [];
		
		$i = false;
		$iService = $this; // We'll need this context
		$reader = new ArrayReader(array($model));

		$writer = new CallbackWriter(
			function ($item) use (&$i, &$iService, &$lines, &$mappings) {
				try {
					$i = $iService->writeItem($item);
					$i->setPaymentMethod('yay this is getting better');
					$iService->updateEntity($i);
					
					$iService->editLines($i, $mappings, $lines);
				} catch (Exception $e) {
					throw $e;
				}
			});
		
		$workflow = new Workflow($reader);
		$workflow->addWriter($writer);
		$converter = new MappingItemConverter();
		//$nestedConverter = new NestedMappingItemConverter('lines');

		foreach ($mappings as $key => $value) {
			$to = $key;
			$from = $value;

			if (is_string($from)) {
				$converter->addMapping($from, $to); // TODO: The mappings need to be reversed for this...
			} elseif (is_array($from) && (array_keys($from) !== range(0, count($from) - 1))) {
				// Only process associative arrays, we need to use a different converter for sequential arrays
				$converter->addMapping($to, array_flip($value));
			} elseif (is_array($from) && (array_keys($from) == range(0, count($from) - 1))) {
				$from[0]['orderProductId'] = 'order_product_id'; // TODO: Mappings aren't working right here!!!
				$from[0]['orderId'] = 'order_product_id'; // TODO: Mappings aren't working right here!!!

				//$nestedConverter->addMapping($to, array_flip($from[0]));
			}
		}

		$workflow->addItemConverter($converter);
		//$workflow->addItemConverter($nestedConverter);
		$workflow->process();
	}
	
	private function editLines(&$entity, $mappings, $lines) {
		$lineIds = array();

		foreach ($lines as $line) {
			$lineIds[] = $line['line_id'];
		}

		// TODO: Lines aren't converting yo
		$lService = new InvoiceLine($this->context->em, 'OcInvoiceLine'); // We'll need this context
		$reader = new ArrayReader($lines);

		// TODO: Turn this back on
		$entityLines = $entity->getLines();
		foreach ($entityLines as $line) {
			$lineId = $line->getLineId();
			if (!in_array($lineId, $lineIds)) {
				$lService->deleteEntity($lineId);
			}
		}
		
		$writer = new CallbackWriter(
			function ($item) use (&$entity, &$lService) {
				try {
					$l = $lService->writeItem($item);
					//$i->setPaymentMethod('yay this is getting better');
					//$iService->updateEntity($i);
					
					$l->setInvoice($entity);
					$l->setProduct($this->getEntityManager()->getRepository('OcProduct')->find($item['product_id']));
					$l->setDetailType('SalesItemLineDetail');
					
					$lService->updateEntity($l);
				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		$workflow->addWriter($writer);

		$converter = new MappingItemConverter();
		//foreach ($mappings['lines'][0] as $key => $value) {
		foreach ($mappings['lines'] as $key => $value) {
			$to = $key;
			$from = $value;

			if (is_string($from)) {
				$converter->addMapping($from, $to); // TODO: The mappings need to be reversed for this...
			} elseif (is_array($from) && (array_keys($from) !== range(0, count($from) - 1))) {
				// Only process associative arrays, we need to use a different converter for sequential arrays
				$converter->addMapping($to, array_flip($value));
			} elseif (is_array($from) && (array_keys($from) == range(0, count($from) - 1))) {
				//$nestedConverter->addMapping($to, array_flip($from[0]));
			}
		}

		$workflow->addItemConverter($converter);
		$workflow->process();
	}

	public function deleteTransaction($model) {
		$iService = $this;
		$i = $this->getEntity($model['invoice']['invoice_id'], false);

		// Just something to trigger transaction load
		$tService = new Transaction($this->context, 'OcTransaction');
		$t = $i->getTransaction();

		$lService = new InvoiceLine($this->context->em, 'OcInvoiceLine'); // We'll need this context
		$lines = $i->getLines();

		try {
			$tService->deleteEntity($t->getTransactionId());

			foreach ($lines as $l) {
				$lService->deleteEntity($l->getLineId());
			}

			// Delete the owning side last
			$iService->deleteEntity($i->getInvoiceId());
		} catch (Exception $e) {
			throw $e;
		}
	}
	
	private static function addDateConverters(&$workflow) {
		$dateConverter = new DateTimeValueConverter();
		$workflow->addValueConverter('dateAdded', $dateConverter);
		$workflow->addValueConverter('dateModified', $dateConverter);
	}
	
	private function mapWorkOrder(&$mappings) {
		// I left a note in the mapDoctrineEntity method regarding nesting
		
		
		$this->context->mapDoctrineEntity($mappings, array(
			'OcInvoice' => array(
				'foreign' => 'Invoice',
				'meta' => $this->iMeta,
				'children' => array(
					'OcInvoiceLine' => array(
						'foreign' => 'Line',
						'meta' => $this->ilMeta
					),
					'OcOrderOption' => array(
						'foreign' => 'Option',
						'meta' => $this->ooMeta
					),
					'OcCustomer' => array(
						'foreign' => 'Customer',
						'meta' => $this->cMeta
					)
				)
			)
		), true, false);
	}
	
	private function mapTransaction(&$mappings) {
		// I left a note in the mapDoctrineEntity method regarding nesting
		// $mappings, $config, $children, $remote
		// Children and map to local
		$this->context->mapDoctrineEntity($mappings, array(
			'OcTransaction' => array(
				'meta' => $this->tMeta,
				'children' => array(
					'OcStore' => array(
						'meta' => $this->sMeta
					)
				)
			)
		), true, false);
	}
}
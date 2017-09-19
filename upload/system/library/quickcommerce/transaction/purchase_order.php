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
use App\Resource\PurchaseOrder;
use App\Resource\PurchaseOrderLine;
//use App\Resource\Language;
use App\Resource\Option;
use App\Resource\ProductOption;
use App\Resource\ProductOptionValue;

use Ddeboer\DataImport\Writer\DoctrineCallbackWriter;
use Doctrine\Common\Util\Debug;

// At a minimum, invoices and cash sales must be supported
/**
 * Class TransactionPurchaseOrder
 */
class TransactionPurchaseOrder extends QcResource {
	protected $className = 'OcPurchaseOrder';

	protected $tMeta;
	protected $poMeta;
	protected $polMeta;
	protected $ooMeta;
	protected $cMeta;
	protected $sMeta;

	private function loadMetadata() {
		$this->tMeta = $this->context->em->getClassMetadata('OcTransaction');
		$this->poMeta = $this->context->em->getClassMetadata('OcPurchaseOrder');
		$this->polMeta = $this->context->em->getClassMetadata('OcPurchaseOrderLine');
		$this->ooMeta = $this->context->em->getClassMetadata('OcOrderOption'); // TODO: Check if there's a quantity?
		$this->cMeta = $this->context->em->getClassMetadata('OcCustomer');
		$this->sMeta = $this->context->em->getClassMetadata('OcStore');
	}
    
    public function search($params = array(), $serialize = true, $tableize = true) {
        $poService = new App\Resource\PurchaseOrder($this->context->em, 'OcPurchaseOrder');
        return $poService->search($params, $serialize, $tableize);
    }

	/**
	 * Creates the base transaction
	 *
	 * @param null $model
	 * @return mixed
	 */
	public function addTransaction($model) {
		$this->loadMetadata();

		// Just something to trigger transaction load
		$tService = new Transaction($this->context, 'OcTransaction');
		$t = new OcTransaction();

		$tModel = array_map(function ($item) {
			return (is_scalar($item)) ? $item : null;
		}, $model);

		$curService = new App\Resource\Currency($this->context->em, 'OcCurrency');
		$sService = new App\Resource\Store($this->context->em, 'OcStore');

		$reader = new ArrayReader(array($tModel));
		//$writer = new DoctrineWriter($this->context->em, 'OcTransaction');
		$writer = new CallbackWriter(
			function ($item) use (&$t, &$tService, &$curService, &$sService) {
				try {
					$t = $tService->writeItem($item); // Set camelize flag to true - input has already been mapped to their camelcase equivalents

					// TODO: Fix writeItem associations
					if (isset($item['currency_id'])) {
						$cur = $curService->getEntity($item['currency_id'], false);
						$t->setCurrency($cur);
					}

					if (isset($item['store_id'])) {
						if ($item['store_id'] > 0) {
							$s = $sService->getEntity($item['store_id'], false);
							$t->setStore($s);
						} else {
							$t->setStoreId(0);
						}
					}

					$date = new DateTime();
					$t->setDateAdded($date);
					$t->setDateModified($date);

					$tService->updateEntity($t);
				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		self::addDateConverters($workflow);
		$workflow->addWriter($writer);
		$workflow->process();

		$transactionId = $t->getTransactionId();
		if ($transactionId == null || !($transactionId > 0)) {
			throw new Exception('Could not create the transaction entity - exiting');
		}

		$mappings = []; // Clear mappings for var reuse

		// I left a note in the mapDoctrineEntity method regarding nesting
		$this->mapPurchaseOrder($mappings);

		// TODO: If transaction id is false we need to throw an error or exception
		$model = $model['purchase_order']; // TODO: Detect type and switch array key accordingly

		// Strip out lines and process separately
		$lines = $model['lines'];
		unset($model['lines']);

		$c = null;
		$cService = null;

		if (isset($model['customer_id'])) {
			$cService = new App\Resource\Customer($this->context->em, 'OcCustomer');

			$customerId = $model['customer_id'];
			$c = $cService->getEntity($customerId, false);
		}

		$output = [];

		$i = false;
		$iService = $this; // We'll need this context
		$reader = new ArrayReader(array($model));

		$writer = new CallbackWriter(
			function ($item) use (&$iService, &$cService, &$t, &$i, &$c, &$lines, &$mappings) {
				try {
					$i = $iService->writeItem($item, false);

					$i->setTransaction($t);
					$i->setPaymentMethod('Cash/Credit Card');

					$purchaseOrderNo = $this->createPurchaseOrderNo(true); // Set flag to check against QB as well

					if (empty($purchaseOrderNo)) {
						// Throw an exception! QBO fill f**k up if you don't have an incremented invoice number
						// And to keep consistency between QBO and QC, we want to make sure we're assigning it on our end

						/*Reference number for the transaction. If not explicitly provided at create time, this field is populated based on the setting of Preferences:CustomTxnNumber as follows:

						If Preferences:CustomTxnNumber is true a custom value can be provided. If no value is supplied, the resulting DocNumber is null.
    					If Preferences:CustomTxnNumber is false, resulting DocNumber is system generated by incrementing the last number by 1.

						If Preferences:CustomTxnNumber is false and a value is supplied, that value is stored even if it is a duplicate. Recommended best practice: check the setting of Preferences:CustomTxnNumber before setting DocNumber.
						Sort order is ASC by default.*/
					} else {
						// Set the invoice number!
						$i->setPurchaseOrderNo($purchaseOrderNo);
					}

					if (isset($c)) {
						$i->setCustomer($c);
					}

					$iService->updateEntity($i);

					$iService->editLines($i, $mappings, $lines, true); // TODO: Last param is a add/edit mode flag - quick fix, see below for details
				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		$workflow->addWriter($writer);
		$workflow->process();

		/*$converter = new MappingItemConverter();
		$nestedConverter = new NestedMappingItemConverter('lines');
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
		$workflow->process();*/

		return $i->getPurchaseOrderId();
	}

	/**
	 * Creates the base transaction
	 *
	 * @param null $model
	 * @return mixed
	 */
	public function editTransaction($model) {
		$this->loadMetadata();

		$i = $this->getEntity($model['purchase_order']['purchase_order_id'], false);

		// Just something to trigger transaction load
		$tService = new Transaction($this->context, 'OcTransaction');
		$t = $i->getTransaction();
		$t->getStoreId(); // Trigger lazy load or this blows up... I should probably get rid of lazy loading on this entity

		// Get the root node
		$tModel = array_map(function ($item) {
			return (is_scalar($item)) ? $item : null;
		}, $model);

		$reader = new ArrayReader(array($tModel));
		$writer = new CallbackWriter(
			function ($item) use (&$t, &$tService) {
				try {
					unset($item['dateAdded']);
					unset($item['dateModified']);

					$tService->fillEntity($item, $t);

					$date = new DateTime();
					$t->setDateAdded(new DateTime($t->getDateAdded()));
					$t->setDateModified($date);

					$tService->updateEntity($t);
				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		self::addDateConverters($workflow);
		$workflow->addWriter($writer);
		$workflow->process();

		// TODO: If transaction id is false we need to throw an error or exception
		$model = $model['purchase_order']; // TODO: Detect type and switch array key accordingly

		// Strip out lines and process separately
		$lines = $model['lines'];
		unset($model['lines']);

		$c = null;
		$cService = null;

		if (isset($model['customer_id'])) {
			$em = $this->getEntityManager();
			$cService = new App\Resource\Customer($em, 'OcCustomer');

			$customerId = $model['customer_id'];
			$c = $cService->getEntity($customerId, false);
		}

		$output = [];

		$iService = $this; // We'll need this context
		$reader = new ArrayReader(array($model));

		$writer = new CallbackWriter(
			function ($item) use (&$iService, &$cService, &$t, &$i, &$c, &$lines, &$mappings) {
				try {
					$iService->fillEntity($item, $i);

					$i->setTransaction($t);
					$i->setPaymentMethod('Cash/Credit Card');

					if (isset($customer)) {
						$i->setCustomer($customer);
					}

					$iService->updateEntity($i);

					$iService->editLines($i, $mappings, $lines);
				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		$workflow->addWriter($writer);
		$workflow->process();
	}

	private function editLines(&$entity, $mappings, $lines, $add = false) {
		$lineIds = array();

		foreach ($lines as $line) {
			$lineIds[] = $line['line_id'];
		}

		$lService = new PurchaseOrderLine($this->context->em, 'OcPurchaseOrderLine'); // We'll need this context
		$reader = new ArrayReader($lines);

		$entityLines = $entity->getLines();
		foreach ($entityLines as $line) {
			$lineId = $line->getLineId();
			if (!in_array($lineId, $lineIds)) {
				$lService->deleteEntity($lineId);
			}
		}

		$pRepo = $this->getEntityManager()->getRepository('OcProduct');
		$tcRepo = $this->getEntityManager()->getRepository('OcTaxClass');

		$writer = new CallbackWriter(
			function ($item) use (&$entity, &$lService, &$pRepo, &$tcRepo, &$add) {
				try {
					// This is a quick fix, and it's VEST stuff anyway
					if (isset($item['revenue'])) {
						$item['revenue'] = (float)$item['revenue'];
					}

					if (isset($item['royalty'])) {
						$item['royalty'] = (float)$item['royalty'];
					}

					if (isset($item['vest'])) {
						$item['vest'] = (float)$item['vest'];
					}

					// Quick fix to make lines save properly - for some reason I have to set the camelize flag  (possibly because the line doesn't exist when adding an invoice)
					/*if ($add) {
						$l = $lService->writeItem($item);
					} else {
						$l = $lService->writeItem($item);
					}*/

					// If an empty string is provided Doctrine will throw an exception saying the type doesn't match - because it doesn't :)
					// TODO: Check to see if null conversion issues were resolved (nothing should be null when it gets to here, but you know...
					if ($item['detail_type'] == 'DescriptionOnlyLineDetail') {
						$item['quantity'] = 0;
					}

					$l = $lService->writeItem($item);

					//$i->setPaymentMethod('yay this is getting better');
					//$iService->updateEntity($i);

					$l->setPurchaseOrder($entity);

					$p = false;
					if (isset($item['product_id'])) {
						$p = $pRepo->find($item['product_id']);
					}

					if ($p) {
						$l->setProduct($p);
					}

					$tc = false;
					if (isset($item['tax_class_id'])) {
						$tc = $tcRepo->find($item['tax_class_id']);
					}

					if ($tc) {
						$l->setTaxClass($tc);
					}

					$lService->updateEntity($l);
				} catch (Exception $e) {
					throw $e;
				}
			});

		$workflow = new Workflow($reader);
		$workflow->addWriter($writer);

		/*$converter = new MappingItemConverter();
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

		$workflow->addItemConverter($converter);*/
		$workflow->process();
	}

	public function deleteTransaction($model) {
		$iService = $this;
		$i = $this->getEntity($model['purchase_order']['purchase_order_id'], false);

		// Just something to trigger transaction load
		$tService = new Transaction($this->context, 'OcTransaction');
		$t = $i->getTransaction();

		$lService = new PurchaseOrderLine($this->context->em, 'OcPurchaseOrderLine'); // We'll need this context
		$lines = $i->getLines();

		try {
			$tService->deleteEntity($t->getTransactionId());

			foreach ($lines as $l) {
				$lService->deleteEntity($l->getLineId());
			}

			// Delete the owning side last
			$iService->deleteEntity($i->getPurchaseOrderId());
		} catch (Exception $e) {
			throw $e;
		}
	}

	protected function createPurchaseOrderNo($fetchQBMax = false) {
		$purchaseOrderNo = null;

		$where = new \Doctrine\ORM\Query\Expr();
		$where = $where->eq('i.invoicePrefix', (new \Doctrine\ORM\Query\Expr())->literal('CT'));
		$result = $this->getMax('i', 'invoiceNo', $where);

		if ($fetchQBMax) {
			//$service = new PurchaseOrderService();
			//$qbResult = $service->query('Select DocNumber from PurchaseOrder ORDERBY DocNumber DESC MAXRESULTS 1');
		}

		if (!empty($result)) {
			$purchaseOrderNo = $result[0]['value'] + 1; // Increment
		}

		return $purchaseOrderNo;
	}

	protected function setValue($entity, $value, $setter) {
		if (method_exists($entity, $setter)) {
			$entity->$setter($value);
		}
	}

	protected function setData(array $item, &$entity) {
		$fieldNames = array_merge($this->poMeta->getFieldNames(), $this->poMeta->getAssociationNames());
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
				|| $value != $this->poMeta->getFieldValue($entity, $fieldName)
			) {
				// Looks like this was done for Doctrine 1.x, it's not working in 2.x (methods like from/toArray were removed... might have something to do with it?
				/*if ($this->poMeta->hasAssociation($fieldName)) {
					// Don't set assoc, it won't work!
					// I need some better checks in here... I might have my mappings incorrectly specified but I'm not getting a relationship type in the association meta
					$associationMapping = $this->poMeta->getAssociationMapping($fieldName);

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

	private static function addDateConverters(&$workflow) {
		$dateConverter = new DateTimeValueConverter();
		$workflow->addValueConverter('dateAdded', $dateConverter);
		$workflow->addValueConverter('dateModified', $dateConverter);
	}

	private function mapPurchaseOrder(&$mappings) {
		$this->context->mapDoctrineEntity($mappings, array(
			'OcPurchaseOrder' => array(
				'foreign' => 'PurchaseOrder',
				'meta' => $this->poMeta,
				'children' => array(
					'OcPurchaseOrderLine' => array(
						'foreign' => 'Line',
						'meta' => $this->polMeta
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
				'foreign' => 'PurchaseOrder',
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
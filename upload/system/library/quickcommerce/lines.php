<?php
require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/doctrine.php');

use App\Resource\Product;
//use App\Resource\Language;
use App\Resource\Option;
use App\Resource\ProductOption;
use App\Resource\ProductOptionValue;

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\Common\Util\Inflector;
use Doctrine\Common\Util\Debug;
use Ddeboer\DataImport\Workflow;
use Ddeboer\DataImport\Reader\ArrayReader;
use Ddeboer\DataImport\Reader\OneToManyReader;
use Ddeboer\DataImport\Writer\ArrayWriter;
use Ddeboer\DataImport\Writer\CallbackWriter;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Ddeboer\DataImport\ItemConverter\MappingItemConverter;
use Ddeboer\DataImport\ItemConverter\NestedMappingItemConverter;
use Ddeboer\DataImport\ValueConverter\DateTimeValueConverter;
use Doctrine\Common\Collections\Collection;

use Zend\Code\Generator\ClassGenerator;
use Zend\Code\Generator\MethodGenerator;
use Zend\Code\Generator\ParameterGenerator;
use Zend\Code\Reflection\ClassReflection;

use Doctrine\Common\Collections\Criteria;

/**
/**
 * Session handler for Line collections
 */
class Lines {
	private $config;
	public $db;
	protected $data = array();
	protected $registry;

	protected $services = array();

	public function __construct($registry) {
		$this->config = $registry->get('config');
		$this->customer = $registry->get('customer');
		$this->tax = $registry->get('tax');
		$this->session = $registry->get('session');

		if (!isset($this->session->data['lines']) || !is_array($this->session->data['lines'])) {
			$this->session->data['lines'] = array();
		}
		
		$di = new DoctrineInitializer($this, $registry);
		
		$this->registry = $registry;
	}
	
	// Yeah, this isn't great but whatever for now
	/**
	 * @param $class
	 * @return bool
     */
	public static function autoloadEntities($class) {
		$file = DIR_QC . 'app/src/Entity/' . str_replace('\\', '/', strtolower($class)) . '.php';
		
		if (is_file($file)) {
			include_once($file);
			return true;
		}
		
		return false;
	}

	/**
	 * @param $class
	 * @return bool
     */
	public static function autoload($class) {
		$file = DIR_SYSTEM . 'library/quickcommerce/vendor/' . str_replace('\\', '/', strtolower($class)) . '.php';
		//var_dump($file);
		
		if (is_file($file)) {
			include_once($file);
			return true;
		}
		
		return false;
	}
	
	public function __get($name) {
		return $this->registry->get($name);
	}
	
	public function saveLines() {
		$lines = $this->getLines(); // Load from session and decorate
		
		$this->load->model('transaction/transaction'); // TODO: Something to load universal admin/catalog models
		$tModel = $this->model_transaction_transaction;
		$tModel->setTransactionType(new TransactionInvoice($this));
		
		$lines = $tModel->getLineItems();
		//var_dump($lines);
	}
	
	public function adjustValue($op, $val, $adj) {
		if (!in_array($op, array('+', '-', '*', '/', '%', '='))) {
			return $val; // If the operation is not recognized, just return the value
		}
		
		if (!is_numeric($val)) {
			return false; // Fail silently
		}
		
		switch ($op) {
			case '+':
				$val += $adj;
				break;
			case '-':
				$val -= $adj;
				break;
			case '*':
				$val = $val * $adj;
				break;
			case '/':
				$val = $val / $adj;
				break;
			case '%':
				$val = $val * $adj / 100;
				break;
			case '=':
				$val = $adj;
				break;
		}
		
		return $val;
	}
	
	protected static function optionData(OcProductOption &$productOption, OcOption &$option, OcProductOptionValue &$productOptionValue) {
		$option = $productOption->getOption();
		$descriptions = $option->getDescription();
		$description = $descriptions->get(0);
		
		$data = array(
			'product_option_id'       => $productOption->getProductOptionId(),
			'product_option_value_id' => '',
			'option_id'               => $option->getOptionId(),
			'name'                    => $description->getName(),
			'type'                    => $option->getType(),
		);
		
		if ($productOptionValue) {
			$data = array_merge($data, array(
				//'option_value_id'         => $optionValue->getOptionValueId(),
				//'value'                   => $optionValue->getName(),
				'quantity'                => $productOptionValue->getQuantity(),
				'subtract'                => $productOptionValue->getSubtract(),
				'price'                   => $productOptionValue->getPrice(),
				'price_prefix'            => $productOptionValue->getPricePrefix(),
				'points'                  => $productOptionValue->getPoints(),
				'points_prefix'           => $productOptionValue->getPointsPrefix(),
				'weight'                  => $productOptionValue->getWeight(),
				'weight_prefix'           => $productOptionValue->getWeightPrefix()
			));
		}
		
		return $data;
	}

	private function fillDiscounts() {
		// Product Discounts
		/*$discount_quantity = 0;

        foreach ($this->session->data['lines'] as $key_2 => $quantity_2) {
            $product_2 = (array)unserialize(base64_decode($key_2));

            if ($product_2['product_id'] == $productId) {
                $discount_quantity += $quantity_2;
            }
        }

        $product_discount_query = $this->db->query("SELECT price FROM " . DB_PREFIX . "product_discount WHERE productId = '" . (int)$productId . "' AND customer_groupId = '" . (int)$this->config->get('config_customer_group_id') . "' AND quantity <= '" . (int)$discount_quantity . "' AND ((date_start = '0000-00-00' OR date_start < NOW()) AND (date_end = '0000-00-00' OR date_end > NOW())) ORDER BY quantity DESC, priority ASC, price ASC LIMIT 1");

        if ($product_discount_query->num_rows) {
            $price = $product_discount_query->row['price'];
        }

        // Stock
        if (!$product_query->row['quantity'] || ($product_query->row['quantity'] < $quantity)) {
            $stock = false;
        })*/
	}

	private function fillOptions($product) {
		$productOptionValueService = $this->services['productOptionValue'];

		// Options
		if (!empty($product['option'])) {
			$options = $product['option'];
		} else {
			$options = array();
		}

		$ids = array_keys($options);

		// Load all options
		$qb = $this->em->createQueryBuilder();
		$qb->select('po')
			->from('OcProductOption', 'po')
			->where('po.product = :product')
			->andWhere('po.productOptionId IN (:ids)')
			->setParameter('ids', $ids)
			->setParameter('product', $product);
		//->leftJoin('OcOption', 'o', 'WITH', 'o.optionId = po.optionId');

		//echo $qb->getQuery()->getDql();
		$productOptions = $qb->getQuery()->getResult();

		foreach ($productOptions as $productOption) {
			$optionValueId = $options[$productOption->getProductOptionId()];
			$optionPrice = 0;
			$optionPoints = 0;
			$optionWeight = 0;

			//Debug::dump($productOption);

			// Get the option and get the description
			$option = $productOption->getOption();

			if ($option) {
				//Debug::dump($option);
				$descriptions = $option->getDescription();
				$description = $descriptions->get(0);

				$optionType = $option->getType();
				$optionPrice = 0;
				$optionPoints = 0;
				$optionWeight = 0;

				if ($optionType == 'select' || $optionType == 'radio' || $optionType == 'image') {
					// Get the product option values
					$productOptionValue = $productOptionValueService->getEntity($optionValueId, false);

					//Debug::dump($productOptionValue);
					if ($productOptionValue) {
						//$description = $value->getDescription()->get(0);
						//$name = $description->getName();
						$optionData[] = self::optionData($productOption, $option, $productOptionValue);
					}

					/*if ($values) {
                        foreach ($values as $value) {
                            $description = $value->getDescription()->get(0);
                            $name = $description->getName();

                            $optionData[] = self::optionData($productOption, $optionValue);
                        }
                    }*/

					/*$optionPrice = $this->adjustValue($option_value_query->row['price_prefix'], $optionPrice, $option_value_query->row['price']);
                    $optionPoints = $this->adjustValue($option_value_query->row['points_prefix'], $optionPoints, $option_value_query->row['points']);
                    $optionWeight = $this->adjustValue($option_value_query->row['weight_prefix'], $optionWeight, $option_value_query->row['weight']);

                    if ($option_value_query->row['subtract'] && (!$option_value_query->row['quantity'] || ($option_value_query->row['quantity'] < $quantity))) {
                        $stock = false;
                    }*/
				} elseif ($productOption->get['type'] == 'checkbox' /*&& is_array($value)*/) {
					$optionValue = $productOptionValueService->getEntity($optionValueId, false);

					$optionData[] = self::optionData($productOption, $optionValue);
				} elseif ($productOption->get['type'] == 'text' || $productOption->get['type'] == 'textarea' || $productOption->get['type'] == 'file' || $productOption->get['type'] == 'date' || $productOption->get['type'] == 'datetime' || $productOption->get['type'] == 'time') {
					$optionData[] = self::optionData($productOption);
				}
			}
		}

		return (is_array($optionData)) ? $optionData : array();
	}

	/**
	 * Retrieves lines from session, merging in product information as needed
	 * TODO: You know me I need to find some convoluted way of doing this dynamically using doctrine associations
	 * Don't bother doing it until I actually need more than just products
	 * This is definitely more efficient, but you know, I like my flexibility
	 */
	public function getLines($children = false) {
		foreach ($this->session->data['lines'] as $key => $quantity) {
			$optionData = array();

			$sessionProduct = unserialize(base64_decode($key));

			$this->data[$key] = array_merge($sessionProduct, array('key' => $key, 'quantity' => $quantity));
		}
		
		return $this->data;
	}

	public function getRecurringProducts() {
		$recurringProducts = array();

		foreach ($this->getLines() as $key => $value) {
			if ($value['recurring']) {
				$recurringProducts[$key] = $value;
			}
		}

		return $recurringProducts;
	}
	
	public function addCommission($productId, $qty = 1, $params) {
		$product = array();
		$product['detail_type'] = 'CommissionLineDetail';

		$product['product_id'] = (int)$productId;
		$product = array_merge($product, $params);

		/*if ($option) {
			$product['option'] = $option;
		}*/

		$key = base64_encode(serialize($product));

		if ((int)$qty && ((int)$qty > 0)) {
			if (!isset($this->session->data['lines'][$key])) {
				$this->session->data['lines'][$key] = (int)$qty;
			} else {
				$this->session->data['lines'][$key] += (int)$qty;
			}
		}
	}

	public function addDescription($description) {
		$product = array();
		$product['detail_type'] = 'DescriptionOnlyLineDetail';

		if (is_string($description)) {
			$product['index'] = rand();
			$product['description'] = $description;
		} elseif (is_array($description)) {
			$product['index'] = rand();
			$product = array_merge($product, $description);
		}

		$key = base64_encode(serialize($product));

		if (!isset($this->session->data['lines'][$key])) {
			$this->session->data['lines'][$key] = '';
		}
	}

	public function addServiceItem($description, $quantity, $price) {
		$product = array();
		$product['detail_type'] = 'ServiceItemLineDetail';

		if (is_string($description)) {
			$product['index'] = rand();
			$product['description'] = $description;
		} elseif (is_array($description)) {
			$product['index'] = rand();
			$product = array_merge($product, $description);
		}

		if ($price) {
			$product['price'] = (float)$price;
		}

		$key = base64_encode(serialize($product));

		if ((int)$quantity && ((int)$quantity > 0)) {
			if (!isset($this->session->data['lines'][$key])) {
				$this->session->data['lines'][$key] = (int)$quantity;
			} else {
				$this->session->data['lines'][$key] += (int)$quantity;
			}
		}
	}

	public function addDescriptionItem($description, $quantity, $price, $taxClassId = null, $tax = false) {
		$product = array();
		$product['detail_type'] = 'DescriptionItemLineDetail';

		if (is_string($description)) {
			$product['index'] = rand();
			$product['description'] = $description;
		} elseif (is_array($description)) {
			$product['index'] = rand();
			$product = array_merge($product, $description);
		}

		if ($price) {
			$product['price'] = (float)$price;
		}

		$product['tax'] = 0.0000;
		if (!empty($taxClassId)) {
			$product['tax_class_id'] = $taxClassId;

			if (is_numeric($tax) && $tax > 0) {
				$product['tax'] = $tax;
			}
		}

		$key = base64_encode(serialize($product));

		if ((int)$quantity && ((int)$quantity > 0)) {
			if (!isset($this->session->data['lines'][$key])) {
				$this->session->data['lines'][$key] = (int)$quantity;
			} else {
				$this->session->data['lines'][$key] += (int)$quantity;
			}
		}
	}

	public function add($productId, $quantity = 1, $option = array(), $price = null, $recurringId = null) {
		$product = array();
		$product['detail_type'] = 'SalesItemLineDetail';
		if (is_numeric($productId)) {
			$product['product_id'] = (int)$productId;
		} elseif (is_array($productId)) {
			$product = array_merge($product, $productId);
		}

		$product['quantity'] = $quantity;

		if ($option) $product['option'] = $option;
		if ($price) $product['price'] = (float)$price;
		if ($recurringId) $product['recurring_id'] = (int)$recurringId;

		$key = base64_encode(serialize($product));

		if ((int)$quantity && ((int)$quantity > 0)) {
			if (!isset($this->session->data['lines'][$key])) {
				$this->session->data['lines'][$key] = (int)$quantity;
			} else {
				$this->session->data['lines'][$key] += (int)$quantity;
			}
		}
	}

	public function update($key, $qty) {
		$this->data = array();

		if ((int)$qty && ((int)$qty > 0) && isset($this->session->data['lines'][$key])) {
			$this->session->data['lines'][$key] = (int)$qty;
		} else {
			$this->remove($key);
		}
	}

	public function remove($key) {
		$this->data = array();

		unset($this->session->data['lines'][$key]);
	}

	public function clear() {
		$this->data = array();

		$this->session->data['lines'] = array();
	}

	public function getWeight() {
		$weight = 0;

		foreach ($this->getLines() as $product) {
			if ($product['shipping']) {
				$weight += $this->weight->convert($product['weight'], $product['weight_class_id'], $this->config->get('config_weight_class_id'));
			}
		}

		return $weight;
	}

	public function getSubTotal() {
		$total = 0;
		
		$lines = $this->getLines(); // Gets lines

		foreach ($lines as $line) {
			$raw = unserialize(base64_decode($line['key']));
			if (isset($raw['mod'])) {
				$total += $this->adjustValue($raw['mod'], $raw['revenue'], $raw['rate']);
			} elseif (isset($line['price'])) {
				$lineTotal = (isset($line['total'])) ? $line['total'] : 0.00;
				$price = (float)$line['price'];

				if ($price == 0.00 && $lineTotal > 0) {
					$total += $lineTotal;
				} else {
					$total += $line['price'] * $line['quantity'];
				}
			}
		}

		return $total;
	}

	public function getSalesItemTaxTotal($line) {
		$taxData = array();
		$total = 0.00;

		if (isset($line['tax_class_id'])) {
			$taxRates = $this->tax->getRates($line['price'], $line['tax_class_id']);

			foreach ($taxRates as $taxRate) {
				if (!isset($taxData[$taxRate['tax_rate_id']])) {
					$taxData[$taxRate['tax_rate_id']] = ($taxRate['amount'] * $line['quantity']);
				} else {
					$taxData[$taxRate['tax_rate_id']] += ($taxRate['amount'] * $line['quantity']);
				}
			}

			foreach ($taxData as $id => $taxTotal) {
				$total += $taxTotal;
			}
		}

		return $total;
	}

	public function getTaxes($lines = null) {
		$lines = (!empty($lines) && count($lines) > 0) ? $lines : $this->getLines(); // Gets lines
		$tax_data = array();

		foreach ($lines as $line) {
			$raw = unserialize(base64_decode($line['key']));

			if (isset($line['tax_class_id'])) {
				$tax_rates = $this->tax->getRates($line['price'], $line['tax_class_id']);

				foreach ($tax_rates as $tax_rate) {
					if (!isset($tax_data[$tax_rate['tax_rate_id']])) {
						$tax_data[$tax_rate['tax_rate_id']] = ($tax_rate['amount'] * $line['quantity']);
					} else {
						$tax_data[$tax_rate['tax_rate_id']] += ($tax_rate['amount'] * $line['quantity']);
					}
				}
			}
		}

		return $tax_data;
	}

	public function getTotal() {
		$total = 0;

		foreach ($this->getLines() as $line) {
			$total += $this->tax->calculate($line['price'], $line['tax_class_id'], $this->config->get('config_tax')) * $line['quantity'];
		}

		return $total;
	}

	public function countProducts() {
		$line_total = 0;

		$lines = $this->getLines();

		foreach ($lines as $line) {
			$line_total += $line['quantity'];
		}

		return $line_total;
	}

	public function hasProducts() {
		return count($this->session->data['lines']);
	}

	public function hasRecurringProducts() {
		return count($this->getRecurringProducts());
	}

	public function hasStock() {
		$stock = true;

		foreach ($this->getLines() as $product) {
			if (!$product['stock']) {
				$stock = false;
			}
		}

		return $stock;
	}
	
	// TODO: Is this even relevant here? Probably not done exactly like the cart is...
	public function hasShipping() {
		$shipping = false;

		foreach ($this->getLines() as $product) {
			if (isset($product['shipping']) && $product['shipping']) {
				$shipping = true;

				break;
			}
		}

		return $shipping;
	}

	public function hasDownload() {
		$download = false;

		foreach ($this->getLines() as $product) {
			if ($product['download']) {
				$download = true;

				break;
			}
		}

		return $download;
	}
}
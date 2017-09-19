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


use Ddeboer\DataImport\Writer\DoctrineCallbackWriter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\Common\Util\Inflector;
use Doctrine\ORM\EntityManager;

require_once dirname(__FILE__) . '/writer.php';

/**
 * Interface ITransaction
 */
interface ITransaction {
	/**
	 * @param $id
	 * @return mixed
     */
	
	//public function getTransaction($id);
	//public function getTransactions();
}

/**
 * Class Resource
 */
abstract class QcResource extends \App\Resource implements ITransaction {
	protected $context;
	protected $oc;
	protected $className;
	protected $base;
	
	/**
     * List of fields used to lookup an entity
     *
     * @var array
     */
    protected $lookupFields = array();

	/**
	 * @param $id
     */
	public function _load($id) {
		$this->create();
	}
    
    public abstract function search($params = array(), $serialize = true, $tableize = true);

	/**
	 
	 * @param Controller $context
	 * @param null $id
     */
	function __construct($context, $id = null, $index = null) {
		$this->context = $context;

		parent::__construct($this->context->em, $this->className);
		
		$this->oc = new ModelResource(new Registry()); // Accessor for OpenCart model
		
		if ($id != null && is_int($id) && $id > 0) {
			// Load the ID
			//$this->load($id);
		} else {
			//$this->create($id);
		}
		
		if ($index) {
			if (is_array($index)) {
				$this->lookupFields = $index;
			} else {
				$this->lookupFields = array($index);
			}
		}
	}
	/**
	 * @return Controller
     */
	public function getContext() {
		return $this->context;
	}

	/**
	 * @param $id
	 * @return mixed
     */
	public function convert($id) {
		if (method_exists($this, '__convert')) {
			return $this->__convert($id);
		}
	}
	
	/**
     * Add the associated objects in case the item have for persist its relation
     *
     * @param array $item
     * @param $entity
     * @return void
     */
    /*protected function loadAssociationObjectsToEntity(array $item, $entity)
    {
        foreach ($this->meta->getAssociationMappings() as $associationMapping) {

            $fieldVal = (isset($item[$associationMapping['fieldName']])) ? $item[$associationMapping['fieldName']] : null;
            $value = null;

            if (isset($fieldVal)) {
                if (is_int($fieldVal) || is_string($fieldVal)) {
                    $value = $this->entityManager->getReference($associationMapping['targetEntity'], $item[$associationMapping['fieldName']]);
                }
            }

            if (null === $value) {
                continue;
            }

            $setter = 'set' . ucfirst($associationMapping['fieldName']);
            $this->setValue($entity, $value, $setter);
        }
    }*/
}
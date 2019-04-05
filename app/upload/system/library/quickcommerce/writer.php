<?php
namespace Ddeboer\DataImport\Writer;

require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_QC . 'vendor/ddeboer/data-import/src/Ddeboer/DataImport/Writer/WriterInterface.php');
require_once(DIR_QC . 'vendor/ddeboer/data-import/src/Ddeboer/DataImport/Writer/AbstractWriter.php');
require_once(DIR_QC . 'vendor/ddeboer/data-import/src/Ddeboer/DataImport/Writer/DoctrineWriter.php');


use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\ClassMetadata;
use Ddeboer\DataImport\Writer\DoctrineWriter;
use Doctrine\ORM\ORMException;

/**
 * A bulk Doctrine writer
 *
 * See also the {@link http://www.doctrine-project.org/docs/orm/2.1/en/reference/batch-processing.html Doctrine documentation}
 * on batch processing.
 *
 * @author David de Boer <david@ddeboer.nl>
 */
class DoctrineCallbackWriter extends DoctrineWriter
{
    /**
     * @var callable
     */
    private $callback;

    /**
     * Constructor
     *
     * @param EntityManager $entityManager
     * @param string        $entityName
     * @param string|array        $index         Field or fields to find current entities by
     */
    public function __construct(EntityManager $entityManager, $entityName, $callback, $index = null)
    {
        $this->entityManager = $entityManager;
        $this->entityName = $entityName;
        $this->entityRepository = $entityManager->getRepository($entityName);
        $this->entityMetadata = $entityManager->getClassMetadata($entityName);
        
		if (!is_callable($callback)) {
            throw new \RuntimeException('$callback must be callable');
        }
		
		$this->callback = $callback;
		
		if($index) {
            if(is_array($index)) {
                $this->lookupFields = $index;
            } else {
                $this->lookupFields = array($index);
            }
        }
    }
	
    public function writeItem(array $item)
    {
        $this->counter++;
        $entity = $this->findOrCreateItem($item);

        $this->loadAssociationObjectsToEntity($item, $entity);
        $this->updateEntity($item, $entity);

        $this->entityManager->persist($entity);
		
		if (isset($this->callback)) {
            call_user_func($this->callback, $entity, $item, $this);
        }
		
		//$this->entityManager->persist($entity);

        //if (($this->counter % $this->batchSize) == 0) {
            $this->flushAndClear();
            //$this->flush();
        //}

        return $this;
    }

    /**
     * Finds existing entity or create a new instance
     */
    protected function findOrCreateItem(array $item)
    {
        $entity = null;
        // If the table was not truncated to begin with, find current entity
        // first
        if (false === $this->truncate) {
            if ($this->lookupFields) {
                $lookupConditions = array();
                foreach ($this->lookupFields as $fieldName) {
                    $lookupConditions[$fieldName] = $item[$fieldName];
                }
                $entity = $this->entityRepository->findOneBy(
                    $lookupConditions
                );
            } else {
                //$entity = $this->entityRepository->find(current($item)); // This is how it's done in the original library
                $params = array(); // This works better because the first key may not be the id
                foreach ($this->entityMetadata->identifier as $id) {
                    if (isset($item[$id])) {
                        $params[$id] = $item[$id];
                    }
                }

                if (!count($params) > 0) {
                    return $this->getNewInstance();
                }

                $entity = $this->entityRepository->find($params);
            }
        }

        if (!$entity) {
            return $this->getNewInstance();
        }

        return $entity;
    }

    public function flush() {
        $this->entityManager->flush();
    }

    public function clear() {
        $this->entityManager->clear($this->entityName);
    }

	/**
     * Add the associated objects in case the item have for persist its relation
     *
     * @param array $item
     * @param $entity
     * @return void
     */
    protected function loadAssociationObjectsToEntity(array $item, $entity)
    {
        foreach ($this->entityMetadata->getAssociationMappings() as $associationMapping) {

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
    }
	
	/**
     * 
     * @param array $item
     * @param object $entity
     */
    protected function updateEntity(array $item, $entity)
    {
        $fieldNames = array_merge($this->entityMetadata->getFieldNames(), $this->entityMetadata->getAssociationNames());
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
                || $value != $this->entityMetadata->getFieldValue($entity, $fieldName)
            ) {
                // Looks like this was done for Doctrine 1.x, it's not working in 2.x (methods like from/toArray were removed... might have something to do with it?
                if ($this->entityMetadata->hasAssociation($fieldName)) {
					// Don't set assoc, it won't work!
                    // I need some better checks in here... I might have my mappings incorrectly specified but I'm not getting a relationship type in the association meta
                    $associationMapping = $this->entityMetadata->getAssociationMapping($fieldName);

                    if (is_array($value) && (array_keys($value) == range(0, count($value) - 1))) {
                        // Indexed array - target is a collection
                        // Processing in a callback is a better option -- this can get way too convoluted
                        /*$collection = $entity->{'get'. ucfirst($fieldName)}(); // Get the collection
                        foreach ($value as $collectionItem) {
                            //$ref = $this->entityManager->getReference($associationMapping['targetEntity'], $collectionItem);
                            $ref = new $associationMapping['targetEntity'];
                            $this->loadAssociationObjectsToEntity($collectionItem, $ref);
                            $this->updateEntity($collectionItem, $ref);
                            var_dump($collectionItem);
                            $this->entityManager->persist($ref);
                            $collection->add($ref);
                        }*/
                    } else {
                        $value = $this->entityManager->getReference($associationMapping['targetEntity'], $value);
						
						$setter = 'set' . ucfirst($fieldName);
						$this->setValue($entity, $value, $setter);
                    }
                } else {
					$setter = 'set' . ucfirst($fieldName);
					$this->setValue($entity, $value, $setter);
				}

                //$setter = 'set' . ucfirst($fieldName);
                //$this->setValue($entity, $value, $setter);
            }
        }        
    }
}

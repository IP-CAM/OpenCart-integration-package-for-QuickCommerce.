<?php
require_once(DIR_QC . 'vendor/autoload.php');
require_once(DIR_SYSTEM . 'library/quickcommerce/doctrine.php');

use Doctrine\ORM\Configuration;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Mapping\NamingStrategy;
use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Tools;
use Doctrine\ORM\Tools\Setup;
use Doctrine\ORM\Tools\EntityGenerator;
use Doctrine\ORM\Tools\DisconnectedClassMetadataFactory;
use Doctrine\Common\Util\Inflector;

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
//use App\Entity\OpenCart;

class ObjectFactory {
	/**
	 * @param $em
	 * @param $className
	 * @param $data
	 * @return mixed
     */
	public static function createObject(&$em, $className, $data = null) {
		$object = false;
		// TODO: Initialize defaults - this is currently being implemented in qccontroller
		if (substr($className, 0, strlen('QuickBooks_IPP_Object_')) != 'QuickBooks_IPP_Object_') {
			$className = 'QuickBooks_IPP_Object_' . $className;
			
			$object = new $className;
		}
		
		/*if ($object && is_array($data)) {
			if ($object != false) {
				$object->{'set' . $current}($data[$columns[$local]]);
			}
		}*/
		
		return $object;
	}
	
	/**
	 * Creates an OpenCart entity
	 */
	public static function createEntity(&$em, $className, $data, $children = false) {
		$entity = array(); // For now this returns an array... I'll return an actual entity when Doctrine is working 100%
		$nullable = false;
		$default = null;
		$metadata = $em->getClassMetadata($className);
		
		foreach ($metadata->fieldMappings as $prop => $meta) {
			if (isset($meta['nullable']) && $meta['nullable'] == true) {
				$nullable = true;
			}
			
			if (isset($meta['options']) && $meta['options'] == true) {
				if (isset($meta['options']['default'])) {
					$default = $meta['options']['default'];
				}
			}
			
			switch ($meta['type']) {
				case 'boolean':
					$entity[$meta['columnName']] = (bool)$default;
					break;
				case 'integer':
					$entity[$meta['columnName']] = (int)$default;
					break;
				case 'decimal':
					$entity[$meta['columnName']] = (float)$default;
					break;
				case 'string':
					$entity[$meta['columnName']] = (string)$default;
					break;
				case 'date':
					$entity[$meta['columnName']] = (string)$default;
					break;	
			}
			
			if (isset($data[$meta['columnName']])) $entity[$meta['columnName']] = $data[$meta['columnName']];

			$default = null;
		}

		$tableize = true;
		
		//var_dump('Creating classname = ' . $className);
		if ($className == 'OcCustomer') {
			//var_dump('entity');
			//var_dump($entity);			
		}
		
		if ($children || is_array($children)) self::loadAssoc($em, $metadata, $entity, $children);
		
		if ($className == 'OcCustomer') {
			//var_dump('final');
			//var_dump($entity);			
		}
		
		return $entity;
	}
	
	/**
	 * @param $em
	 * @param $className
	 * @param $data
	 * @return array
     */
	
	
	// This should be an entity method but, since entities are only array objects a.t.m.
	// OpenCart doesn't have entities (what???!)
	// TODO: Lazy loading, and DI instead of class name
	/**
	 * Loads OpenCart associations
	 *
	 * @param $em
	 * @param $className
	 * @param $entity
	 * @param $data
     */
	public static function loadAssoc(&$em, &$metadata, &$entity, $children = false) {
		// TODO: Lazy load?
		$nullable = false;
		$default = null;
		//var_dump($metadata);
		//var_dump('load assoc');
		//var_dump($metadata->associationMappings);

		$include = false;
		if (property_exists($metadata, 'associationMappings')) {
			foreach ($metadata->associationMappings as $prop => $meta) {
				if ((is_bool($children) && $children == true) || (is_array($children) && array_key_exists($prop, $children))) {
					$include = true;
				}
				
				// TODO: Can we even set as nullable?
				/*if (isset($meta['nullable']) && $meta['nullable'] == true) {
					$nullable = true;
				}*/
				
				/*if (isset($meta['options']) && $meta['options'] == true) {
					if (isset($meta['options']['default'])) {
						$default = $meta['options']['default'];
					}
				}*/
				
				if ($include && isset($meta['targetEntity'])) { // TODO: Make sure class exists maybe?
					$child = self::createEntity($em, $meta['targetEntity'], $children[$prop], false); // TODO: Blank data for now
					// TODO: Throw error if no child
					if (isset($meta['sourceToTargetKeyColumns'])) {
						foreach ($meta['sourceToTargetKeyColumns'] as $col => $targetCol) {
							$entity[$col] = $child[$targetCol];
							$entity[$prop] = $child;
						}
					}
					//$meta['targetEntity'];
					//$children[$meta['targetEntity']] = $entity;
				}
				
				$include = false;
				//var_dump($child);
			}
			
			//if (isset($data[$meta['columnName']])) $entity[$meta['columnName']] = $data[$meta['columnName']];
		}
	}
}

class DoctrineEntityMapper {
	// Being able to nest like this would be cool, but I don't know if there will be issues 
	// with the mapping converter if there are similarly named keys...
	/*$this->context->mapDoctrineEntity($mappings, array(
		'OcTransaction' => array(
			'foreign' => 'Transaction',
			'meta' => $tMeta,
			'children' => array(
				'OcWorkOrder' => array(
					'foreign' => 'WorkOrder',
					'meta' => $iMeta,
					'children' => array(
						'OcWorkOrderLine' => array(
							'foreign' => 'Line',
							'meta' => $ilMeta
						),
						'OcOrderOption' => array(
							'foreign' => 'Option',
							'meta' => $ooMeta
						),
						'OcCustomer' => array(
							'foreign' => 'Customer',
							'meta' => $cMeta
						)
					)
				)
			)
		)
	), 2);*/
	/**
	 * Creates mappings for converting a Doctrine entity to/from its OpenCart counter-part
	 * @param $context
	 * @param $mappings
	 * @param array $config
	 * @param bool|false $children
	 */
	public static function mapDoctrineEntity(&$context, &$mappings, $config = array(), $children = false) {
		
		// How many levels of nesting?
		// 0: Root only
		// 1: One level
		// n: n levels
		$children = abs((int)$children);
		
		$entityName = key($config);
		$params = $config[$entityName];
		$meta = $params['meta'];
		
		$mapping = array_fill_keys(array_keys($meta->fieldMappings), null);

		// Just tableize the field name if we're mapping a local entity
		foreach ($mapping as $field => $col) {
			$mapping[$field] = Inflector::tableize($field);
		}

		if (($children != 0) && array_key_exists('children', $params)) {
			$assoc = $meta->getAssociationMappings();

			foreach ($assoc as $field) {
				$childEntityName = (string)$field['targetEntity'];

				//if ($childEntityName != 'OcStore') continue; // TODO: TEMP Fix

				// Children should have been provided in params
				if (array_key_exists($childEntityName, $params['children'])) {
					$childMapping = [];

					$context->mapDoctrineEntity($childMapping, array(
						$childEntityName => $params['children'][$childEntityName]
					), false, false); // Do not automatically map children and it's a local op too

					$mapping[$field['fieldName']] = $childMapping;
				}
			}

			// TODO: Other association types?
		}
		
		$mappings = array_merge($mappings, $mapping);
	}
	
	// Just in case I missed something, this has been copied from qc controller before I deleted it
	/*// TODO: Make some kind of Doctrine helper, or move this to EntityMapper class
	// I'm going to turn this into a static method too
	public function mapDoctrineEntity(&$mappings, $config = array(), $children = false, $foreign = true) {	
		// How many levels of nesting?
		// 0: Root only
		// 1: One level
		// n: n levels
		$children = abs((int)$children);
		
		$entityName = key($config);
		$params = $config[$entityName];
		$meta = $params['meta'];
		
		$mapping = array_fill_keys(array_keys($meta->fieldMappings), null);

		// Mapping against the remote entity
		if ($foreign) {
			// Get relevant XML node(s);
			$xpath = '//entity[@foreign="' . $params['foreign'] . '"]';
			// TODO: Check to see if key exists - if anything's missing throw an Exception
			foreach($this->mapXml->xpath($xpath) as $map) {
				$fields = $map->xpath('./field | ./id');

				// TODO: This is very similar to EntityMapper::mapFields
				// I might want to do make this a utility method
				foreach ($fields as $field) {
					$attributes = $field->attributes();
					$col = (string)$attributes['column'];
					$field = (string)$attributes['name'];
					$type = (string)$attributes['type'];

					// Is the local model property a complex type?
					//var_dump(isset($attributes['column']));
					if ($field && isset($attributes['column'])) {
						//var_dump($name . ' => ' . $foreign); // Keep this one in here
						//var_dump($attributes);
						if ($field && $type && array_key_exists($field, $mapping))
							$mapping[$field] = $col;
					}

				}

				if (($children != 0) && array_key_exists('children', $params)) {
					$assoc = $map->xpath('./many-to-one');

					foreach ($assoc as $field) {
						$attributes = $field->attributes();
						$type = (string)$attributes['type'];
						$joinColAttr = false;
						$field = (string)$attributes['field'];
						$childEntityName = (string)$attributes['target-entity'];

						// Children should have been provided in params
						if (array_key_exists($childEntityName, $params['children'])) {
							$childMapping = [];


							$this->mapDoctrineEntity($childMapping, array(
								$childEntityName => $params['children'][$childEntityName]
							), false); // Do not automatically map children

							$mapping[$field] = $childMapping;
						}
					}

					$assoc = $map->xpath('./one-to-many');

					foreach ($assoc as $field) {
						$attributes = $field->attributes();
						$type = (string)$attributes['type'];
						$joinColAttr = false;
						$field = (string)$attributes['field'];
						$childEntityName = (string)$attributes['target-entity'];

						// Children should have been provided in params
						if (array_key_exists($childEntityName, $params['children'])) {
							$childMapping = [];

							$this->mapDoctrineEntity($childMapping, array(
								$childEntityName => $params['children'][$childEntityName]
							), false); // Do not automatically map children

							$mapping[$field] = array($childMapping);
						}
					}
				}
			}
		} else {
			// Just tableize the field name if we're mapping a local entity
			foreach ($mapping as $field => $col) {
				$mapping[$field] = Inflector::tableize($field);
			}
			
			if (($children != 0) && array_key_exists('children', $params)) {
				$assoc = $meta->getAssociationMappings();
				
				foreach ($assoc as $field) {
					$childEntityName = (string)$field['targetEntity'];

					if ($childEntityName == 'OcStore') continue; // TODO: TEMP Fix for what?

					// Children should have been provided in params
					if (array_key_exists($childEntityName, $params['children'])) {
						$childMapping = [];

						$this->mapDoctrineEntity($childMapping, array(
							$childEntityName => $params['children'][$childEntityName]
						), false, false); // Do not automatically map children and it's a local op too

						$mapping[$field['fieldName']] = $childMapping;
					}
				}

				// TODO: Other association types?
			}
		}
		
		$mappings = array_merge($mappings, $mapping);
	}*/
}

/**
 * Class EntityMapper
 */
class EntityMapper {
	/**
	 * @param $em
	 * @param $foreign
	 * @param $mapXml
	 * @param $mappings
	 * @param bool|false $export
     */
	public static function mapEntities(&$em, $foreign, $mapXml, &$mappings, $export = false) {
		if (is_string($foreign)) {
            $foreignEntities = $mapXml->xpath('//entity[@foreign="' . $foreign . '"]');
			foreach ($foreignEntities as $match) {
				//var_dump($match);
				self::mapEntity($em, $match, $mappings, $export);
				//echo 'end match ----------------------------------------';
			}
        } elseif (is_array($foreign) && count($foreign) > 0) {
            foreach ($foreign as $key) {
                $foreignEntities = $mapXml->xpath('//entity[@foreign="' . $foreign . '"]');
                self::mapEntity($em, $foreignEntities[0], $mappings, $export);
            }
        }
    }

	/**
	 * @param $em
	 * @param $map
	 * @param $mappings
	 * @param bool|false $export
     */
	public static function mapEntity(&$em, $map, &$mappings, $export = false) {
		// Get the mapping info for the entity
		$className = (string)$map->attributes()->name;
		$foreignName = (string)$map->attributes()->foreign;
		
		$fieldMap = (isset($mappings[$foreignName]['fields']) && is_array($mappings[$foreignName]['fields'])) ? $mappings[$foreignName]['fields'] : null;
		
		// TODO: Put this in the doctrine driver
		$metadata = $em->getClassMetadata($className);
		$associations = $metadata->associationMappings;
		
		$domNode = dom_import_simplexml($map);
		$nodePath = $domNode->getNodePath();
		
		$mappings[$foreignName]['foreign'] = $foreignName;
		$mappings[$foreignName]['fields'] = array_fill_keys(array_keys($metadata->fieldMappings), null);
		$mappings[$foreignName]['assoc'] = array_fill_keys(array_keys($metadata->associationMappings), null);
		
		// TODO: Call decorate() here?
		//$mappings[$foreignName]['refs'] = ...
		
		// Get properties
		$fields = $map->xpath('./field | ./id');
		self::mapFields($mappings[$foreignName], $fields); // Create bindings for local fields that are mapped to entity fields
		self::mapObjects($mappings[$foreignName], $fields); // Create bindings for local fields that are mapped to object props
		
		$refs = $map->xpath('./ref');
		self::mapRefs($mappings[$foreignName], $refs);
		
		// TODO: START HERE WEDNESDAY
		$assoc = $map->xpath('./many-to-one');
		self::mapObjects($mappings[$foreignName], $assoc); // Create bindings for local fields that reference other entities that are mapped to foreign objects
		//var_dump($assoc);
		/*
		array (size=3)
		  0 => 
			object(SimpleXMLElement)[273]
			  public '@attributes' => 
				array (size=4)
				  'field' => string 'address' (length=7)
				  'target-entity' => string 'OcAddress' (length=9)
				  'fetch' => string 'LAZY' (length=4)
				  'foreign' => string '' (length=0)
			  public 'join-columns' => 
				object(SimpleXMLElement)[278]
				  public 'join-column' => 
					object(SimpleXMLElement)[279]
					  public '@attributes' => 
						array (size=2)
						  'name' => string 'address_id' (length=10)
						  'referenced-column-name' => string 'address_id' (length=10)
		*/
		//self::mapFields($mappings[$foreignName], $assoc);
		
		$tableize = false; // TODO: What is this for again?
		
		if (isset($metadata)) {
			if (property_exists($metadata, 'associationMappings')) {		
				foreach ($metadata->associationMappings as $field => $mapping) {
					$key = ($tableize == true) ? Inflector::tableize($field) : $field;
				}
			}
		}

		if ($export != true) {
			$mappings[$foreignName]['fields'] = array_flip(array_filter($mappings[$foreignName]['fields'], function($v) {
				return !is_null($v);
			}));
		} else {
			$mappings[$foreignName]['fields'] = array_filter($mappings[$foreignName]['fields'], function($v) {
				return !is_null($v);
			});
		}

		if (!is_null($fieldMap)) {
			$mappings[$foreignName]['fields'] = array_merge($fieldMap, $mappings[$foreignName]['fields']);
		}
	}

	/**
	 * @param $mappings
	 * @param $fields
     */
	public static function mapFields(&$mappings, $fields) {
		foreach ($fields as $field) {
			$attributes = $field->attributes();
			$foreignProperty = (string)$attributes['foreign'];
			$type = (string)$attributes['type'];
			$col = (string)$attributes['name'];
			$objMappings = false;
			$joinCol = false;

			if ($foreignProperty) {
				// Multiple remote bindings
				if ($foreignProperty == '*' && $field->foreign->count() > 0) {
					$objMappings = array();
					foreach ($field->foreign as $foreign) {
						$local = (string)$foreign->attributes()->local;
						$objMappings[$local] = (string)$foreign;
					}

					$foreignProperty = false;
				}

				if (isset($attributes['column'])) {
					if (is_string($foreignProperty)) {
						if (count(explode('->', $foreignProperty)) == 1) {
							if ($col && $type && array_key_exists($col, $mappings['fields'])) {
								$mappings['fields'][$col] = $foreignProperty;
							}
						}
					} elseif (is_array($objMappings)) {
						foreach ($objMappings as $local => $foreign) {
							if (count(explode('->', $foreign)) == 1) {
								if ($col && array_key_exists($col, $mappings['fields'])) {
									$mappings['fields'][$col][$local] = $foreign;
								}
							}
						}
					}
				}
			}
		}
	}
	
	/**
	 * @param $mappings
	 * @param $refs
     */
	public static function mapRefs(&$mappings, $refs) {
		if ($refs) $mappings['refs'] = array();
		
		foreach ($refs as $ref) {
			$attributes = $ref->attributes();
			$foreign = (string)$attributes['foreign'];
			// Until Doctrine entity code generation is done, we're going to have to map column name as the entity will not contain these referenced properties
					// I don't want them in there either, I'd prefer to decorate the entity object as opposed to coding in references
			//$col = (string)$attributes['name'];
			$col = (string)$attributes['column'];
			$type = (string)$attributes['type'];
			
			/*var_dump($attributes);
			var_dump($foreign);
			var_dump($col);
			var_dump($type);*/
			 
			// Is the local model property a complex type?
			//var_dump(isset($attributes['column']));
			if ($foreign && isset($attributes['column'])) {
				//var_dump($name . ' => ' . $foreign); // Keep this one in here
				//var_dump($attributes);
				
				// TODO: Check against table metadata - I started a method somewhere called decorate()
				if ($col && $type /*&& array_key_exists($col, $mappings['refs']))*/)
					// Until Doctrine entity code generation is done, we're going to have to map column name as the entity will not contain these referenced properties
					// I don't want them in there either, I'd prefer to decorate the entity object as opposed to coding in references
					//$mappings['refs'][$col] = $foreign;
					$mappings['refs'][$col] = $foreign;
			}
		}
	}
	
	// TODO: Need to add a callback parameter to do some of the logic in here
	/**
	 * Called by mapFields
	 *
	 * @param $mappings
	 * @param $fields
     */
	public static function mapObjects(&$mappings, &$fields) {
		//var_dump($mappings);
		foreach ($fields as $field) {
			$attributes = $field->attributes();
			$foreignProperty = (string)$attributes['foreign'];
			$type = (string)$attributes['type'];
			$col = (string)$attributes['name'];
			$objMappings = false;
			$joinCol = false;

			// TODO: This has been checked for edit op only, it will probably need to be updated for add
			// TODO: $col is not an accurate name
			if ($foreignProperty) {
				// Multiple remote bindings
				if ($foreignProperty == '*' && $field->foreign->count() > 0) {
					$objMappings = array();
					foreach ($field->foreign as $foreign) {
						$local = (string)$foreign->attributes()->local;
						$objMappings[$local] = (string)$foreign;
					}

					$foreignProperty = false;
				}

				if (isset($attributes['column'])) {
					if (is_string($foreignProperty)) {
						if (count(explode('->', $foreignProperty)) > 1) {
							if ($col && $type && array_key_exists($col, $mappings['fields'])) {
								$mappings['objects'][$col] = $foreignProperty;
							}
						}
					} elseif (is_array($objMappings)) {
						foreach ($objMappings as $local => $foreign) {
							if (count(explode('->', $foreign)) > 1) {
								if ($col && array_key_exists($col, $mappings['fields'])) {
									$mappings['objects'][$col][$local] = $foreign;
								}
							}
						}
					}
				} elseif (isset($attributes['target-entity'])) {
					$col = (string)$attributes['field'];
					if ($col && array_key_exists($col, $mappings['assoc'])) {
						$mappings['assoc'][$col] = $foreignProperty;
					}
				}
			}
		}
	}

	/**
	 * @param $data
	 * @param null $filter
	 * @return DOMDocument
     */
	public static function filterEntities($data, $filter = null) {
		if (!$filter) return $data;
		
		//header('Content-Type: text/xml; charset=utf-8');
		//echo $data;
		//exit;
		
		// TODO: Return the path to node in original doc so we can do stuff like attach categories and such later
		$xml = new DOMDocument();
		$xml->loadXML($data, LIBXML_PARSEHUGE);
		
		$xpath = new DOMXPath($xml);
		
		// TODO: Some sort of sanitization, and strip out nested stuff we don't wanna parse
		/*$nodes = $xpath->query($xpathToNode);
		foreach ($nodes as $node) {
			// Clean nodes using $xpathToNode = '', $xpathToChildren = ''
			if ($xpathToChildren !== '') {
				$children = $xpath->query($xpathToChildren, $node);
				foreach ($children as $child)  $node->removeChild($child);
			};

			$children = null;
		}*/
		
		$collection = new DOMDocument();
		$collection->loadXML('<entities/>');
		foreach ($xpath->query($filter) as $item) {
			$node = $collection->importNode($item, true);
			$collection->documentElement->appendChild($node);
		}
		
		return $collection;
	}
}

final class QC {
	/**
	 * @throws \Doctrine\ORM\Mapping\MappingException
     */
	public static function regenerateEntities() {
		/* @var $entityManager \Doctrine\ORM\EntityManager */
		// this code is just shoot-and-throw-away, so please don't look at the CCLOC

		/* @var $metadataClass \Doctrine\ORM\Mapping\ClassMetadata */

		spl_autoload_register('self::autoload');
		spl_autoload_register('self::autoloadEntities');
		spl_autoload_extensions('.php');

		$paths = array(DIR_QC . 'app/doctrine/orm/mappings/');
		$isDevMode = false;

		// the connection configuration
		$dbParams = array(
			'host'	   => (defined('DB_HOSTNAME')) ? DB_HOSTNAME : '127.0.0.1',
			'driver'   => 'pdo_mysql',
			'user'     => (defined('DB_USERNAME')) ? DB_USERNAME : 'root',
			'password' => (defined('DB_PASSWORD')) ? DB_PASSWORD : 'v@der!4201986',
			'dbname'   => (defined('DB_DATABASE')) ? DB_DATABASE : 'quickcommerce',
			'port'	   => (defined('DB_PORT')) ? DB_PORT : 3306
		);

		$config = Setup::createXMLMetadataConfiguration($paths, $isDevMode);
		$config->setAutoGenerateProxyClasses(true);

		//$namingStrategy = new OpenCartNamingStrategy();
		//$namingStrategy->setPrefix('oc2_');
		//$config->setNamingStrategy($namingStrategy);

		$entityManager = EntityManager::create($dbParams, $config);
		$entityManager->getConnection()->getDatabasePlatform()->registerDoctrineTypeMapping('enum', 'string');

		$cmf = new DisconnectedClassMetadataFactory();
		$cmf->setEntityManager($entityManager);
		$metadatas = $cmf->getAllMetadata();
		//$metadatas = MetadataFilter::filter($metadatas, $input->getOption('filter'));

		// Process destination directory
		$destPath = realpath(DIR_QC . 'app/src/Entity/');

		if ( ! file_exists($destPath)) {
			throw new \InvalidArgumentException(
				sprintf("Entities destination directory '<info>%s</info>' does not exist.", $destPath)
			);
		}

		if ( ! is_writable($destPath)) {
			throw new \InvalidArgumentException(
				sprintf("Entities destination directory '<info>%s</info>' does not have write permissions.", $destPath)
			);
		}

		if (count($metadatas)) {
			// Create EntityGenerator
			$entityGenerator = new EntityGenerator();

			$entityGenerator->setGenerateAnnotations(true);
			$entityGenerator->setGenerateStubMethods(true);
			$entityGenerator->setRegenerateEntityIfExists(true);
			$entityGenerator->setUpdateEntityIfExists(true);
			$entityGenerator->setNumSpaces(4);
			$entityGenerator->setBackupExisting(false);

			//if (($extend = $input->getOption('extend')) !== null) {
				//$entityGenerator->setClassToExtend($extend);
			//}

			/*(foreach ($metadatas as $metadata) {
				$output->writeln(
					sprintf('Processing entity "<info>%s</info>"', $metadata->name)
				);
			}*/

			// Generating Entities
			$entityGenerator->generate($metadatas, $destPath);

			// Outputting information message
			//$output->writeln(PHP_EOL . sprintf('Entity classes generated to "<info>%s</INFO>"', $destPath));
		} else {
			//$output->writeln('No Metadata Classes to process.');
		}

		foreach ($entityManager->getMetadataFactory()->getAllMetadata() as $metadataClass) {
			//if ($metadataClass->getName() != 'OcProduct') continue;

			$requiredParameters = [];
			$optionalParameters = [];

			//var_dump($metadataClass->getName());
			//var_dump($metadataClass->getAssociationNames());
			//echo '------------------------';


			foreach ($metadataClass->getAssociationNames() as $fieldName) {
				if ($metadataClass->isSingleValuedAssociation($fieldName)) {

					$parameter = new ParameterGenerator($fieldName, $metadataClass->getAssociationTargetClass($fieldName));
					$associationMapping = $metadataClass->getAssociationMapping($fieldName);

					if (
						isset($associationMapping['joinColumns'][0]['nullable'])
						&& $associationMapping['joinColumns'][0]['nullable']
					) {
						$parameter->setDefaultValue(null);
						$optionalParameters[] = $parameter;
					} else {
						$requiredParameters[] = $parameter;
					}

					continue;
				}

				$requiredParameters[] = new ParameterGenerator($fieldName, Collection::class);
			}

			foreach ($metadataClass->getFieldNames() as $fieldName) {
				if (
					in_array($fieldName, $metadataClass->getIdentifierFieldNames(), true)
					&& $metadataClass->isIdGeneratorIdentity()
				) {
					// auto-incremental identifier, skip it.
					continue;
				}

				$fieldMapping = $metadataClass->getFieldMapping($fieldName);

				$type = null;

				if ('datetime' === $fieldMapping['type']) {
					$type = 'DateTime';
				}

				$parameter = new ParameterGenerator($fieldName, $type);

				if (isset($fieldMapping['nullable']) && $fieldMapping['nullable']) {
					$parameter->setDefaultValue(null);
					$optionalParameters[] = $parameter;
				} else {
					$requiredParameters[] = $parameter;
				}
			}
			
			//var_dump($metadataClass->getName());
			$reflection = new ClassReflection($metadataClass->getName());
			//var_dump($reflection);
			$classGenerator = ClassGenerator::fromReflection($reflection);
			
			/*$classGenerator->addMethodFromGenerator(new MethodGenerator(
				'__construct',
				array_merge($requiredParameters, $optionalParameters),
				MethodGenerator::FLAG_PUBLIC,
				implode(
					"\n",
					array_map(
						function (ParameterGenerator $parameterGenerator) {
							$name = $parameterGenerator->getName();

							return '$this->' . $name . ' = $' . $name . ';';
						},
						array_merge($requiredParameters, $optionalParameters)
					)
				)
			));*/

			/*header('Content-Type: text/plain');

			echo $classGenerator->generate();
			exit;*/
			
			/*file_put_contents(
				$metadataClass->getReflectionClass()->getFileName(),
				"<?php\n\n\n" . $classGenerator->generate()
				preg_replace(
					'/private \\$([A-Za-z0-9]+) = null;/i',
					'private \$${1};',
					"<?php\n\n\n" . $classGenerator->generate()
				)
			);*/

			//echo $metadataClass->getReflectionClass()->getFileName();
			
			file_put_contents(
				$metadataClass->getReflectionClass()->getFileName(),
				"<?php\n\n\n" . $classGenerator->generate()
			);
		}
		
		/*$e = $entityManager->find('OcProduct', 72); // Macap M4 DOSER
		$id = $e->getProductId();
		$d = $e->getDescription();
		//var_dump(get_class_methods($d));
		var_dump(count($d));
		foreach ($d as $lang) {
			
			$description = $lang->getDescription();
			$name = $lang->getName();
			var_dump($name);
			//var_dump($description);
		}*/
	}
	
	// Yeah, this isn't great but whatever for now
	/**
	 * @param $class
	 * @return bool
     */
	public static function autoloadEntities($class) {
		$file = DIR_QC . 'app/src/Entity/' . str_replace('\\', '/', strtolower($class)) . '.php';
		
		if (is_file($file)) {
			//var_dump(true);
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
			//var_dump(true);
			include_once($file);
			return true;
		}
		
		return false;
	}
}
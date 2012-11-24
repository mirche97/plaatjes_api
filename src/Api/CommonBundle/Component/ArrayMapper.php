<?php

namespace Api\CommonBundle\Component;

/**
 * ArrayMapper
 * 
 * Maps an array that is derived from JSON or XML to the specified object
 * 
 * TROUBLESHOOTING:
 * 
 * - Entity/class properties that are in a different namespace must be defined with a fully qualified path
 *   after the @var declaration. The same goes for \DateTime etc.
 */
class ArrayMapper
{
    /**
     * Property type filter
     * 
     * Example (skips private properties):
     * \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED
     * 
     * @var integer
     */
    protected $propertyTypeFilter;
    
    /**
     * @var string
     */
    protected $dateTimeStringFormat = 'Y-m-d H:i:s';
    
    /**
     * @var object $entityManager
     */
    protected $entityManager;
    
    /**
     * Constructor
     * 
     * @param object $entityManager
     */
    public function __construct($entityManager = null)
    {
        $this->propertyTypeFilter = \ReflectionProperty::IS_PUBLIC | \ReflectionProperty::IS_PROTECTED;
        $this->entityManager = $entityManager;
    }
    
    /**
     * Sets the property type filter
     * 
     * @param integer $filter
     */
    public function setPropertyTypeFilter($filter)
    {
        $this->propertyTypeFilter = $filter;
    }
    
    /**
     * Returns the property type filter
     * 
     * @return integer
     */
    public function getPropertyTypeFilter()
    {
        return $this->propertyTypeFilter;
    }
    
    /**
     * Sets the date time string format
     * 
     * @param string $format
     */
    public function setDateTimeStringFormat($format)
    {
        $this->dateTimeStringFormat = $format;
    }
    
    /**
     * Returns the date time string format
     * 
     * @return string
     */
    public function getDateTimeStringFormat()
    {
        return $this->dateTimeStringFormat;
    }
    
    /**
     * Sets the specified entity manager
     * 
     * @param object $entityManager
     */
    public function setEntityManager($entityManager)
    {
        $this->entityManager = $entityManager;
    }

    /**
     * Returns an entity manager
     * 
     * @return object
     */
    public function getEntityManager()
    {
        return $this->entityManager;
    }
    
    /**
     * Maps the specified array data to the specified object
     * 
     * @param array  $data
     * @param object $object
     */
    public function map(array $data, $object)
    {
        if (is_object($object)) {
            $refObject = new \ReflectionObject($object);
            $namespace = $refObject->getNamespaceName();
            $properties = $refObject->getProperties($this->propertyTypeFilter);
            
            // Iterate over the public and protected object properties and check if they exist in the data array
            foreach ($properties as $property) {
                $property->setAccessible(true);
                $propertyName = $property->getName();
                $propertyValue = $property->getValue($object);
                
                $dataValue = null;

                // Determine and set data value
                if (isset($data[$propertyName]) && is_array($data[$propertyName])
                    && array_key_exists('@cdata', $data[$propertyName])
                ) {
                    $dataValue = $data[$propertyName]['@cdata'];
                } elseif (isset($data[$propertyName])) {
                    $dataValue = $data[$propertyName];
                }

                // Check if data value must be set on the object
                if (null !== $dataValue) {
                    $methodName = 'set' . ucfirst($propertyName);
                    if (method_exists($object, $methodName . 'FromRequest')) {
                        /**
                         * This method call uses the raw value from the request data array
                         * to support complete customization.
                         */
                        $object->{$methodName . 'FromRequest'}($dataValue);
                    } elseif (method_exists($object, $methodName)) {
                        $this->mapPropertyDataType($property, $dataValue, $namespace);
                        $object->$methodName($dataValue);
                    } else {
                        if ($property->isPublic() || $refObject->hasMethod('__set')) {
                            $this->mapPropertyDataType($property, $dataValue, $namespace);
                            $object->$propertyName = $dataValue;
                        }
                    }
                }
            }
            
            // Call exchangeArray method if the object has one, for customized handling
            if (method_exists($object, 'exchangeArray')) {
                $object->exchangeArray($data);
            }
        }
    }
    
    /**
     * Maps the property data type
     * 
     * @param \ReflectionProperty $property
     * @param mixed  $dataValue
     * @param string $namespace
     */
    protected function mapPropertyDataType($property, &$dataValue, $namespace = null)
    {
        $dataType = $this->getPropertyDataType($property);
        
        if (null !== $dataType) {
            switch ($dataType) {
                case 'string':
                    $dataValue = (string) $dataValue;
                    break;
                case 'float':
                case 'double':
                case 'real':
                    $dataValue = (float) $dataValue;
                    break;
                case 'integer':
                case 'int':
                    $dataValue = (int) $dataValue;
                    break;
                case 'boolean':
                case 'bool':
                    if (!is_bool($dataValue)) {
                        $dataValue = (bool) (strtolower((string) $dataValue) == 'false' ? false : $dataValue);
                    }
                    break;
                case '\DateTime':
                case 'DateTime':
                    // Add hours, minutes, seconds if only the date part is specified
                    if (strlen($dataValue) == 10) {
                        $dataValue .= ' 00:00:00';
                    }
                    $dataValue = \DateTime::createFromFormat($this->dateTimeStringFormat, $dataValue);
                    break;
                case 'array':
                    // No conversion required
                    break;
                default:
                    if ($this->checkAndMapDoctrineEntityDataType($dataType, $dataValue, $namespace)) {
                        break;
                    }
                    
                    if ($this->checkAndMapDoctrineArrayCollectionDataType($dataType, $dataValue, $namespace)) {
                        break;
                    }
                    
                    if ($this->checkAndMapClassDataType($dataType, $dataValue, $namespace)) {
                        break;
                    }
                    
                    if ($this->checkAndMapMultiClassDataType($dataType, $dataValue, $namespace)) {
                        break;
                    }
            }
        }
    }
    
    /**
     * Checks if data type is a Doctrine entity
     * 
     * If the Doctrine entity is detected, then mapping will occur.
     * 
     * @param  string $dataType
     * @param  string $dataValue
     * @param  string $namespace
     * @return boolean
     */
    protected function checkAndMapDoctrineEntityDataType($dataType, &$dataValue, $namespace = null)
    {
        $isDoctrineEntityDataType = false;
        
        // Check if data type is a fully qualified class name
        if ('\\' == substr($dataType, 0, 1)) {
            $class = $dataType;
        } else {
            // Append data type to current namespace
            $class = '\\' . ltrim($namespace . '\\' . $dataType, '\\');
        }
        
        if (class_exists($class)) {
            $refClass = new \ReflectionClass($class);
            $comment = $refClass->getDocComment();
            if (stripos($comment, '@ORM\Entity') !== false) {
                $this->mapDoctrineEntityDataType($class, $dataValue);
                $isDoctrineEntityDataType = true;
            }
        }
        
        return $isDoctrineEntityDataType;
    }
    
    /**
     * Checks if data type is a Doctrine ArrayCollection
     * 
     * If the Doctrine ArrayCollection is detected, then mapping will occur.
     * 
     * @param  string $dataType
     * @param  mixed  $dataValue
     * @param  string $namespace
     * @return boolean
     */
    protected function checkAndMapDoctrineArrayCollectionDataType($dataType, &$dataValue, $namespace = null)
    {
        $isDoctrineArrayCollection = false;
        
        if (stripos($dataType, 'ArrayCollection') === 0) {
            $arrayCollection = new \Doctrine\Common\Collections\ArrayCollection();
            
            $entityClassName = str_replace(array('ArrayCollection', '<', '>'), array('', '', ''), $dataType);
            
            // Check if entity class name is a fully qualified class name
            if ('\\' == substr($entityClassName, 0, 1)) {
                $entityClass = $entityClassName;
                $parts = explode('\\', $entityClassName);
                $entityClassName = array_pop($parts);
            } else {
                // Append entity class name to current namespace
                $entityClass = '\\' . ltrim($namespace . '\\' . $entityClassName, '\\');
            }
            
            if (is_array($dataValue) && class_exists($entityClass)) {
                /**
                 * Check if a collection is found under the key with the name of $entityClassName,
                 * otherwise it's supplied as an indexed array
                 */
                if (isset($dataValue[lcfirst($entityClassName)])) {
                    $dataValue = $dataValue[lcfirst($entityClassName)];
                }
                
                foreach ($dataValue as $entry) {
                    $data = $entry;
                    $this->mapDoctrineEntityDataType($entityClass, $data);
                    
                    // Data has become an entity, add it to the ArrayCollection
                    $arrayCollection->add($data);
                }
            }
        
            $dataValue = $arrayCollection;
            $isDoctrineArrayCollection = true;
        }
        
        return $isDoctrineArrayCollection;
    }
    
    /**
     * Maps the Doctrine entity data type
     * 
     * @param string $class Full class path
     * @param mixed  $dataValue
     */
    protected function mapDoctrineEntityDataType($class, &$dataValue)
    {
        // Data type is a Doctrine entity
        $arrayMapper = new self($this->entityManager);
        $doctrineObject = new $class();

        // Map array data to the Doctrine object
        $arrayMapper->map($dataValue, $doctrineObject);

        /**
         * The doctrine object is now in a detached state. If no ID is found, you'll have to
         * determine in your business logic if you want to attach it to the entity manager. 
         * 
         * @link http://docs.doctrine-project.org/en/2.0.x/reference/working-with-objects.html#merging-entities
         */
        
        // Try to find the ID field and load it through the entity manager if available
        if ($this->entityManager instanceof \Doctrine\ORM\EntityManager) {
            $criteria = array();
            
            $refObject = new \ReflectionObject($doctrineObject);
            
            $metaData = $this->entityManager->getClassMetaData(get_class($doctrineObject));
            $propertyNames = $metaData->identifier;
            
            // Loop through the properties (is an array because composite primary keys are supported)
            foreach ($propertyNames as $propertyName) {
                if (isset($dataValue[$propertyName])) {
                    $criteria[$propertyName] = $dataValue[$propertyName];
                } else {
                    $criteria[$propertyName] = null;
                }
            }
            
            if (count($criteria) > 0) {
                // Load the entity by the identifier criteria
                $doctrineObject = $this->entityManager->getRepository('\\' . ltrim($metaData->rootEntityName, '\\'))
                    ->findOneBy($criteria);
                
                if (!is_object($doctrineObject)) {
                    throw new \RuntimeException(sprintf('Associated entity "%s" not found with criteria field(s) "%s"',
                        $refObject->getShortName(), implode('", "', array_keys($criteria))));
                }
            } else {
                // Entity cannot be loaded because no identifiers were found
                throw new \RuntimeException(sprintf('No identifiers found for associated entity "%s"',
                    $refObject->getShortName()));
            }
        }

        $dataValue = $doctrineObject;
    }
    
    /**
     * Checks if data type is an array of classes
     * 
     * A multi class data type is defined with:
     * 
     * "@var array<ClassName>"
     * 
     * @param  string $dataType
     * @param  mixed  $dataValue
     * @param  string $namespace
     * @return boolean
     */
    protected function checkAndMapMultiClassDataType($dataType, &$dataValue, $namespace = null)
    {
        $isMultiClassDataType = false;
        
        if (stripos($dataType, 'array<') === 0) {
            $array = array();
            
            $className = rtrim(str_replace('array<', '', $dataType), '>');
            
            // Check if class name is fully qualified
            if ('\\' == substr($className, 0, 1)) {
                $class = $className;
                $parts = explode('\\', $className);
                $className = array_pop($parts);
            } else {
                // Append entity class name to current namespace
                $class = '\\' . ltrim($namespace . '\\' . $className, '\\');
            }
            
            if (is_array($dataValue) && class_exists($class)) {
                /**
                 * Check if an array is found under the key with the name of $className,
                 * otherwise it's supplied as an indexed array
                 */
                if (isset($dataValue[lcfirst($className)])) {
                    $dataValue = $dataValue[lcfirst($className)];
                }
                
                foreach ($dataValue as $entry) {
                    $data = $entry;
                    
                    $this->checkAndMapClassDataType($class, $data);
                    
                    // Data has become a class, add it to the array
                    $array[] = $data;
                }
            }
        
            $dataValue = $array;
            $isMultiClassDataType = true;
        }
        
        return $isMultiClassDataType;
    }
    
    /**
     * Checks if data type is a class
     * 
     * @param  string $dataType
     * @param  string $dataValue
     * @param  string $namespace
     * @return boolean
     */
    protected function checkAndMapClassDataType($dataType, &$dataValue, $namespace = null)
    {
        $isExistingClass = false;

        // Check if data type is a fully qualified class name
        if ('\\' == substr($dataType, 0, 1)) {
            $class = $dataType;
        } else {
            // Append data type to current namespace
            $class = '\\' . ltrim($namespace . '\\' . $dataType, '\\');
        }
        
        if (class_exists($class)) {
            $arrayMapper = new self();
            $object = new $class();

            // Map array data to the object
            $arrayMapper->map($dataValue, $object);
        
            $dataValue = $object;
            $isExistingClass = true;
        }
        
        return $isExistingClass;
    }
    
    /**
     * Returns the data type of the specified property
     * 
     * @param \ReflectionProperty $property
     * @return string|null
     */
    protected function getPropertyDataType($property)
    {
        $dataType = null;
        
        $comment = $property->getDocComment();
        preg_match_all('#@(.*?)\n#s', $comment, $annotations);
        $annotations = isset($annotations[1]) ? $annotations[1] : array();

        foreach ($annotations as $annotation) {
            // Check if annotation starts with "var "
            if (stripos($annotation, 'var ') === 0) {
                $parts = explode(' ', $annotation);
                if (isset($parts[1])) {
                    $dataType = $parts[1];
                    break;
                }
            }
        }
        
        return $dataType;
    }
}
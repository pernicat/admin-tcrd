<?php
namespace TCRD\Wrapper;

class CollectionWrapper extends ModelWrapper implements \Iterator, \Countable
{
	/**
	 * 
	 * @var \Google_Collection
	 */
	protected $object;
	
	/**
	 * 
	 * @var array
	 */
	protected $uniqueIndex = array();
	

	/**
	 * 
	 * @var multitype:\TCRD\Wrapper\ModelWrapper
	 */
	protected $wrapped = array();
	
	/**
	 * 
	 * @var integer
	 */
	protected $position = 0;
	
	/**
	 * 
	 * @var string
	 */
	protected $itemClass = '\\TCRD\\Wrapper\\ModelWrapper';
	
	/**
	 * 
	 * @var array
	 */
	protected $itemClassArgs = array();
	
	/**
	 * 
	 * @param \Google_Collection $collection
	 */
	public function __construct(\Google_Collection $collection, $args = null)
	{
		parent::__construct($collection, $args);
	}
	
	/**
	 * 
	 * @param string $class
	 * @throws \Exception
	 * @return \TCRD\Wrapper\ColletionWrapper
	 */
	public function setItemClass($class, $args = null)
	{
		if (0 < count($this->wrapped)) {
			// resets the caching
			$this->wrapped = array();
			$this->uniqueIndex = array();
		}
		
		if (!class_exists($class)) {
			throw new \Exception("Class '$class' does not exist");
		}
		$this->itemClass = $class;
		$this->itemClassArgs = $args;
		return $this;
	}
	
	/**
	 * 
	 * 
	 * @param scalar $field
	 * @param scalar $value
	 * @return \TCRD\Wrapper\ModelWrapper
	 */
	public function findUnique($field, $value)
	{
		$index = $this->getUniqueIndex($field);
		
		if (isset($index[$value])) {
			return $index[$value];
		}
		return null;
	}
	
	/**
	 * 
	 * @param scalar $field
	 * @throws \Exception
	 * @return multitype:\TCRD\Wrapper\ModelWrapper
	 */
	public function getUniqueIndex($field)
	{
		// TODO impliment subFields
		
		if (!isset($this->uniqueIndex[$field])) {
			$this->uniqueIndex[$field] = array();
			
			/* @var $item \TCRD\Wrapper\ModelWrapper */
			foreach ($this as $item) {
				if (!isset($item->$field)) {
					$class = get_class($item->getObject());
					
					throw new \Exception("Field '$field' does not exist for class '$class'");
				}
				$value = $item->$field;
				
				if (!is_scalar($value)) {
					$type = gettype($value);
					throw new \Exception("Field '$field' is not a scalar value, it is a '$type'");
				}
				
				if (isset($this->uniqueIndex[$field][$value])) {
					throw new \Exception("Field '$field' is not unique, '$value' exist more than once");
				}
				
				$this->uniqueIndex[$field][$value] = $item;
			}
			
		}
		return $this->uniqueIndex[$field];
	}
	
	/**
	 * 
	 * @param \Google_Model $item
	 * @return \TCRD\Wrapper\ModelWrapper
	 */
	public function wrapItem(\Google_Model $item)
	{
		return new $this->itemClass($item);
	}

	/**
	 * resets the iterator to zero
	 */
    function rewind() 
    {
        $this->position = 0;
    }

    /**
     * Gets the current item
     * 
     * @return multitype:
     */
    function current() 
    {
        // TODO wrap this;
        return $this->offsetGet($this->position);
    }

    /**
     * returns the current position
     * 
     * @return scalar
     */
    function key() 
    {
        return $this->position;
    }

    /**
     * increments the counter
     */
    function next() 
    {
		++$this->position;
    }

    /**
     * checks to see if the current position is valid
     * 
     * @return boolean
     */
    function valid() 
    {
        return $this->offsetExists($this->position);
    }
	
    /**
     * @return integer
     */
    public function count()
    {
    	// TODO;
    	return count($this->object); 
    }
    
    /**
     * (non-PHPdoc)
     * @see ArrayAccess::offsetExists()
     */
    public function offsetExists($offset)
    {
    	return $this->object->offsetExists($offset);
    }
    
    /**
     * 
     * @param scalar $offset
     * @return \TCRD\Wrapper\ModelWrapper:
     */
    public function offsetGet($offset)
    {
    	if (!isset($this->wrapped[$offset])) {
    		$this->wrapped[$offset] = $this->wrapItem(
    				$this->object->offsetGet($offset));
    	}
    	return $this->wrapped[$offset];
    }
    
    /**
     * 
     * @param scalar $offset
     * @param \TCRD\Wrapper\ModelWrapper $value
     * @throws \Exception
     */
    public function offsetSet($offset,  $value)
    {
    	if (!is_subclass_of($value, '\\TCRD\\Wrapper\\ModelWrapper')) {
    		$class = get_class($value);
    		throw new \Exception('blaw');
    	}
    	
    	$this->wrapped[$offset] = $value;
    	$this->object->offsetSet($value->getObject());
    }
    
    /**
     * 
     * @param scalar $offset
     */
    public function offsetUnset($offset)
    {
    	if (isset($this->wrapped[$offset])) {
    		unset($this->wrapped[$offset]);
    	}
    	$this->object->offsetUnset($offset);
    }
	
}
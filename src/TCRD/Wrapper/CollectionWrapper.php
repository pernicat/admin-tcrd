<?php
namespace TCRD\Wrapper;

class ColletionWrapper implements \Iterator, \Countable, \ArrayAccess
{
	/**
	 * 
	 * @var \Google_Collection
	 */
	protected $collection;
	
	/**
	 * 
	 * @var array
	 */
	protected $uniqueIndex = array();
	

	protected $wrapped = array();
	
	protected $position = 0;
	
	protected $itemClass;
	
	/**
	 * 
	 * @param \Google_Collection $collection
	 */
	public function __construct(\Google_Collection $collection)
	{
		$this->collection = $collection;
	}
	
	
	public function findUnique($field, $value)
	{
		
	}
	
	public function getUniqueIndex($field)
	{
		if (!isset($this->uniqueIndex[$field])) {
			$this->uniqueIndex[$field] = array();
			
			foreach ($this as $item) {
				
			}
			
		}
		return $this->uniqueIndex[$field];
	}
	
	/**
	 * 
	 * @param mixed $item
	 * @return mixed
	 */
	public function wrapItem($item)
	{
		// TODO wrap item
		return $item;
	}

	/**
	 * 
	 */
    function rewind() 
    {
        $this->position = 0;
    }

    /**
     * 
     * @return multitype:
     */
    function current() 
    {
        // TODO wrap this;
        return $this->offsetGet($this->position);
    }

    /**
     * 
     */
    function key() 
    {
        
        return $this->collection->key();
    }

    /**
     * 
     */
    function next() 
    {

        $this->collection->next();
    }

    /**
     * 
     */
    function valid() 
    {

        return $this->collection->valid();
    }
	
    /**
     * 
     */
    public function count()
    {
    	return $this->collection->count();
    }
    
    /**
     * 
     * @param scalar $offset
     */
    public function offsetExists ($offset)
    {
    	return $this->collection->offsetExists($offset);
    }
    
    /**
     * 
     * @param scalar $offset
     * @return multitype:
     */
    public function offsetGet($offset)
    {
    	if (!isset($this->wrapped[$offset])) {
    		$this->wrapped[$offset] = $this->wrapItem(
    				$this->collection->offsetGet($offset));
    	}
    	return $this->wrapped[$offset];
    }
    
    /**
     * 
     * @param scalar $offset
     * @param mixed $value
     * @throws \Exception
     */
    public function offsetSet($offset, $value)
    {
    	if ($this->itemClass && !is_subclass_of($value, $this->itemClass)) {
    		$class = get_class($value);
    		throw new \Exception("value must be subclass of '{$this->itemClass}' but is '$class'");
    	}
    	$this->wrapped[$offset] = $value;
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
    	$this->collection->offsetUnset($offset);
    }
	
}
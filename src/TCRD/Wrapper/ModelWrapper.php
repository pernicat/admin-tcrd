<?php
namespace TCRD\Wrapper;

class ModelWrapper implements \ArrayAccess
{
	/**
	 * 
	 * @var \Google_Model
	 */
	protected $object;
	
	/**
	 * 
	 * @param \Google_Model $object
	 * @param mixed $args
	 */
	public function __construct(\Google_Model $object, $config = null)
	{
		$this->object = $object;
		$this->configure($config);
	}
	
	/**
	 * 
	 * @param array $array
	 * @return \TCRD\Wrapper\ModelWrapper
	 */
	public function hydrate($array)
	{

	}
	
	protected function recursiveHydrate($object, $key, $value)
	{
		$class = get_class($object);
		
		
		$className = $class . ucfirst($key);
		if (class_exists($className)) {
			$myClass;
		}
		
		
		
		
		
		
		
		
		
		
		
		foreach ($array as $key => $value) {
			
		
		}
		
		
		
		foreach ($array as $key => $value) {
			if (!is_array($value)) {
				$this->object->$key = $value;
				continue;
			}
			if (isset($value[0])) {
				$this->object->$key = $value;

			}
			
			
		}
		return $this;
	}
	
	/**
	 * 
	 * @return multitype:\TCRD\Wrapper\mixed
	 */
	public function toArray()
	{
		return $this->recursiveArray(get_object_vars($this->object));
	}
	
	/**
	 * 
	 * @param $array
	 * @return multitype:mixed
	 */
	protected function recursiveArray($array)
	{
		foreach ($array as $key => $value) {
			if (is_object($value)) {
				$value = get_object_vars($value);
			}
			
			if (is_array($value)) {
				$array[$key] = $this->recursiveArray($value);
			}
		}
		
		return $array;
	}
	
	/**
	 * 
	 * @param mixed $config
	 * @return \TCRD\Wrapper\ModelWrapper
	 */
	protected function configure($config)
	{
		if (!$config) {
			return $this;
		}
		if (!is_array($config) && !($config instanceof \Traversable)) {
			return $this;
		}
		
		foreach ($config as $key => $value) {
			
			$methodName = 'set' . ucfirst($value);
			
			if (method_exists($this, $methodName)) {
				call_user_func(array($this, $methodName));
			}
		}
		// TODO error checking;
		
		return $this;
	}
	
	/**
	 * 
	 * @return Google_Model
	 */
	public function getObject()
	{
		return $this->object;
	}
	
	/**
	 * 
	 * @param scalar $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->object->$key;
	}
	
	/**
	 * 
	 * @param scalar $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		$this->object->$key = $value;
	}
	
	/**
	 * 
	 * @param unknown $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		return isset($this->object->$key);
	}
	
	/**
	 * 
	 * @param scalar $key
	 */
	public function __unset($key)
	{
		unset($this->object->$key);
	}
	
	/**
	 * 
	 * @param string $name
	 * @param array $args
	 * @return mixed
	 */
	public function __call($name, $args)
	{
		return call_user_func_array(array($this->object, $name), $args);
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
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{
		return $this->object->offsetGet($offset);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		$this->object->offsetSet($offset, $value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		$this->object->offsetUnset($offset);
	}
}
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
		$this->recursiveHydrate($this->object, $array, true);
		return $this;
	}
	
	/**
	 * 
	 * @param \Google_Model $object
	 * @param array $array
	 * @param boolean $hasMethods
	 * @return Google_Model
	 */
	protected function recursiveHydrate(\Google_Model $object, $array, $hasMethods = false)
	{
		foreach ($array as $key => $value) {
				
			$methodName = 'hydrate' . ucfirst($key);
			if ($hasMethods && method_exists($this, $methodName)) {
				$object->$key = $this->$methodName($value);
				continue;
			}
				
			if (!is_array($value)) {
				$object->$key = $value;
				continue;
			}
				
			$className = get_class($object) . ucfirst($key);
			if (class_exists($className)) {
				$myClass = new $className();
				$object->$key = $this->recursiveHydrate($myClass, $value);
			}
		}
		return $object;
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
		$methodName = 'get' . ucfirst($key);
		if (method_exists($this, $methodName)) {
			return $this->$methodName();
		}
		
		return $this->object->$key;
	}
	
	/**
	 * 
	 * @param scalar $key
	 * @param mixed $value
	 */
	public function __set($key, $value)
	{
		$methodName = 'set' . ucfirst($key);
		if (method_exists($this, $methodName)) {
			$this->$methodName($value);
			return;
		}
		$this->object->$key = $value;
	}
	
	/**
	 * 
	 * @param unknown $key
	 * @return boolean
	 */
	public function __isset($key)
	{
		$methodName = 'get' . ucfirst($key);
		if (method_exists($this, $methodName)) {
			return true;
		}
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
		return $this->__isset($offset);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetGet()
	 */
	public function offsetGet($offset)
	{	
		return $this->__get($offset);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetSet()
	 */
	public function offsetSet($offset, $value)
	{
		$this->__set($offset, $value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see ArrayAccess::offsetUnset()
	 */
	public function offsetUnset($offset)
	{
		$this->__unset($offset);
	}
}
<?php
namespace TCRD\Filter;

abstract class FilterAbstract implements FilterInterface
{
	/**
	 * 
	 * @var boolean
	 */
	protected $change = false;
	
	/**
	 * 
	 * @var mixed
	 */
	protected $value;
	
	/**
	 * 
	 * @var array
	 */
	protected $messages = array();
	
	/**
	 * 
	 * @var string
	 */
	protected $original;
	
	/**
	 * 
	 * @param mixed $value
	 * @return \TCRD\Filter\FilterAbstract
	 */
	public function setValue($value)
	{
		$this->value = $value;
		$this->original = $value;
		$this->change = false;
		$this->messages = array();
		return $this;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	public function getOriginal()
	{
		return $this->original;
	}
	
	/**
	 * 
	 * @return multitype:string
	 */
	public function getMessages()
	{
		return $this->messages;
	}
	
	/**
	 * 
	 * @return \TCRD\Filter\FilterAbstract
	 */
	protected function updateValue($value, $msg = null) 
	{
		if (is_array($msg)) {
			$this->messages = array_merge($this->messages, $msg);
		}
		elseif (is_string($msg)) {
			$this->messages[] = $msg;
		}
		elseif (null === $msg) {
			$this->messages[] = "'{$this->value}' -> '{$value}'";
		}
		else {
			throw new \Exception('Unknown $msg type');
		}
		
		$this->original = $this->value;
		$this->value = $value;
		
		$this->change = true;
		return $this;
	}
	
	/**
	 * 
	 * @return boolean
	 */
	public function hasChanged() 
	{
		return $this->change;
	}
}
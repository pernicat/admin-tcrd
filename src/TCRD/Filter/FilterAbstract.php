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
	 * @param mixed $value
	 * @return \TCRD\Filter\FilterAbstract
	 */
	public function setValue($value)
	{
		$this->change = false;
		$this->messages = array();
		return $this;
	}
	
	public function getValue()
	{
		return $this->value;
	}
	
	/**
	 * 
	 * @param string $msg
	 */
	public function addMessage($msg)
	{
		$this->messages[] = $msg;
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
	public function setChange() 
	{
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
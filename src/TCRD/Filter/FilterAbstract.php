<?php
namespace TCRD\Filter;

abstract class FilterAbstract
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
	 * @param unknown $value
	 * @return \TCRD\Filter\FilterAbstract
	 */
	public function setValue($value)
	{
		$this->change = false;
		$this->messages = array();
		return $this;
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
	 * @return mixed;
	 */
	public function filter();
	
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
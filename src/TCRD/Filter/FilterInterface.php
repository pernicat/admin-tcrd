<?php
namespace TCRD\Filter;

interface FilterInterface
{
	/**
	 *
	 * @param mixed $value
	 * @return \TCRD\Filter\FilterInterface
	 */
	public function setValue($value);
	
	/**
	 *
	 * @return array
	 */
	public function getMessages();
	
	/**
	 *
	 * @return boolean
	 */
	public function hasChanged();
	
	/**
	 *
	 * @return mixed;
	 */
	public function filter();
	
	/**
	 * 
	 * @return mixed
	 */
	public function getValue();
	
}

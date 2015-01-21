<?php
namespace TCRD\Filter\Data;

class DataFilter extends \TCRD\Filter\FilterAbstract
{
	/**
	 * 
	 * @var array
	 */
	protected $filters = array();
	
	/**
	 * 
	 * @param string $field
	 * @param \TCRD\Filter\FilterInterface $filter
	 * @return \TCRD\Filter\Data\DataFilter
	 */
	public function addFilter($field, \TCRD\Filter\FilterInterface $filter)
	{
		if (!array_key_exists($field, $this->filters)) {
			$this->filters[$field] = array();
		}
		$this->filters[$field][] = $filter;
		
		return $this;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \TCRD\Filter\FilterAbstract::setValue()
	 */
	public function setValue($value)
	{
		if (!is_array($value)) {
			throw new \Exception("DataFilter value must be an array.");
		}
		return parent::setValue($value);
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \TCRD\Filter\FilterInterface::filter()
	 */
	public function filter()
	{
		foreach ($this->filters as $key => $value) {
			$this->filterField($key);
		}
		return $this->getValue();
	}
	
	/**
	 * 
	 * @param string $field
	 * @throws \Exception
	 * @return array
	 */
	public function filterField($field)
	{
		if (!array_key_exists($field, $this->filters)) {
			return $this->value;
		}
		
		if (!array_key_exists($field, $this->value)) {
			throw new \Exception("Field '$field' does not exist.");
		}
		
		/* @var $filter \TCRD\Filter\FilterInterface */
		foreach ($this->filters[$field] as $filter) {
			$filter->setValue($this->value[$field]);
			$filter->filter();
			
			if ($filter->hasChanged()) {
				$this->setChange();
				$this->messages = array_merge($this->messages, $filter->getMessages());
				$this->value[$field] = $filter->getValue();
			}
		}
		
		return $this->value;
	}
}
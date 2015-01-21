<?php
namespace TCRD\Filter;

class CsvFilter extends FilterAbstract
{
	/**
	 * 
	 * @var FilterInterface
	 */
	protected $filter;
	
	/**
	 * 
	 * @param FilterInterface $filter
	 */
	public function __construct(FilterInterface $filter)
	{
		$this->filter = $filter;
	}
	
	public function filter()
	{
		$exploded = explode(',', $this->value);
		
		$exploded = array_map('trim', $exploded);
		
		$imploded = implode(', ', $exploded);
		
		if ($imploded !== $this->value) {
			$this->updateValue($trimed, "Filtering '{$this->value}' fixing comma spacing.");
		}
		
		$values = array();
		foreach ($exploded as $value) {
			
			$this->filter->setValue($value);
			$values[] = $this->filter->filter();
			
			if ($this->filter->hasChanged()) {
				$this->messages = array_merge($this->messages, $this->filter->getMessages());
				$this->change = true;
			}
		}
		if ($this->hasChanged()) {
			$this->updateValue(implode(', ', $values));
		}
		
		return $this->getValue();
	}
}
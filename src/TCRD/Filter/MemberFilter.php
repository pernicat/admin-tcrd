<?php
namespace TCRD\Filter;

class MemberFilter extends FilterAbstract
{
	/**
	 * 
	 * @var number
	 */
	const LEVEN_LIMIT = 3;
	
	/**
	 * 
	 * @var \TCRD\Worksheet\Roster
	 */
	protected $roster;
	
	/**
	 * 
	 * @param \TCRD\Worksheet\Roster $roster
	 */
	public function __construct(\TCRD\Worksheet\Roster $roster)
	{
		$this->roster = $roster;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \TCRD\Filter\FilterAbstract::filter()
	 */
	public function filter()
	{
		$lower = strtolower($this->value);
		
		if ($lower !== $this->value) {
			$this->setChange();
			$this->addMessage("Filtering '{$this->value}' to lowercase.");
			$this->value = $lower;
		}
		
		if ($this->roster->findUsername($this->value)) {
			return $this->value;
		}
		
		$levin = $this->roster->findClosestUsername(
				$this->value, 
				self::LEVEN_LIMIT);
		
		if (!$levin) {
			return $this->value;
		}
		
		
		$this->setChange();
		$this->addMessage("Filtering '{$this->value}' to closest match '$leven'.");
		return $this->value = $levin;
	}
}
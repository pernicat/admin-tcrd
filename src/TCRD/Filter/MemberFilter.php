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
		
		$trimed = trim($this->value);
		
		if ($trimed !== $this->value) {
			$this->updateValue($trimed, "Filtering '{$this->value}' removing whitespace.");
		}
		
		$lower = strtolower($this->value);
		
		if ($lower !== $this->value) {
			$this->updateValue($lower, "Filtering '{$this->value}' to lowercase.");
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
		
		$this->updateValue($levin, "Filtering '{$this->value}' to close match '$levin'.");
		
		return $this->getValue();
	}
}
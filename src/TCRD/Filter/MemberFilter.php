<?php
namespace TCRD\Filter;

class MemberFilter extends FilterAbstract
{
	protected $roster;
	
	public function __construct(\TCRD\Worksheet\Roster $roster)
	{
		$this->roster = $roster;
	}
	
	public function filter()
	{
		$newValue = strtolower($this->value);
		
		$this->value;
	}
}
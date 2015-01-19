<?php
namespace TCRD\Validator;

use TCRD\Worksheet\Roster;
class UsernameValidator extends ValidatorAbstract
{
	/**
	 * 
	 * @var \TCRD\Worksheet\Roster
	 */
	protected $roster;
	
	/**
	 * 
	 * @param \TCRD\Worksheet\Roster $roster
	 */
	public function __construct(Roster $roster)
	{
		$this->roster = $roster;
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \TCRD\Validator\ValidatorInterface::isValid()
	 */
	public function isValid()
	{
		if ($this->value != trim($this->value)) {
			$this->errors[] = "'{$this->value}' contains whitespace, please remove.";
			return false;
		}
		
		$userEntity = $this->roster->findUsername($this->value);
		if (!$userEntity) {
			$closest = $this->roster->findClosestUsername($this->value);
			
			$this->errors[] = "'{$this->value}' not found, " .
					"did you mean '$closest'?";
			return false;
		}
		return true;
	}
}
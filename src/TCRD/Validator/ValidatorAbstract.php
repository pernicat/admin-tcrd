<?php
namespace TCRD\Validator;

abstract class ValidatorAbstract implements ValidatorInterface
{
	/**
	 *
	 * @var string
	 */
	protected $value;
	
	/**
	 *
	 * @var array
	 */
	protected $errors;
	
	/**
	 * (non-PHPdoc)
	 * @see \TCRD\Validator\ValidatorInterface::setValue()
	 */
	public function setValue($value)
	{
		$this->value = $value;
		$this->errors = array();
	}
	
	/**
	 * (non-PHPdoc)
	 * @see \TCRD\Validator\ValidatorInterface::getErrors()
	 */
	public function getErrors()
	{
		return $this->errors;
	}
}
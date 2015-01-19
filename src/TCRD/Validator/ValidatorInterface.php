<?php
namespace TCRD\Validator;

interface ValidatorInterface
{
	/**
	 * 
	 * @return array
	 */
	public function getErrors();
	
	/**
	 * 
	 * @param string $value
	 */
	public function setValue($value);
	
	/**
	 * 
	 * @return bool
	 */
	public function isValid();
}
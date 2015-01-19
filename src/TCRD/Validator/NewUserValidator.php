<?php
namespace TCRD\Validator;

class NewUserValidator extends ValidatorAbstract
{
	
	/**
	 * (non-PHPdoc)
	 * @see \TCRD\Validator\ValidatorInterface::isValid()
	 */
	public function isValid() 
	{
		$givenName = $this->value['givenname'];
		
		if (!$this->value['givenname']) {
			$this->errors[] = "Given Name is not set.";
			return false;
		}
		
		if ($givenName !== trim($givenName)) {
			$this->errors[] = "Given Name '$giveName' contains whitespace.";
			return false;
		}
		
		
		$familyName = $this->value['familyname'];
		
		if (!$this->value['familyname']) {
			$this->errors[] = "Family Name is not set.";
			return false;
		}
		
		if ($familyName !== trim($familyName)) {
			$this->errors[] = "Family Name '$familyName' contains whitespace.";
			return false;
		}
		
		
		$email = $this->value['personale-mail'];
		
		if (!$email) {
			$this->errors[] = "Personal E-mail is not set.";
			return false;
		}
		
		if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
			$this->errors[] = "'$email' is not a valid e-mail address.";
			return false;
		}
		
		return true;
	}
	
}
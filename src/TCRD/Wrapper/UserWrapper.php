<?php
namespace TCRD\Wrapper;

class UserWrapper extends ModelWrapper
{
	public function __construct(\Google_Service_Directory_User $object, $config = array())
	{
		parent::__construct($object, $config);
	}
	
	/**
	 * 
	 * @param array $array
	 * @return \TCRD\Wrapper\Google_Model
	 */
	public function hydrateAddresses($array)
	{
		foreach ($array as $key => $addressArray) {
			$addressObject = new \Google_Service_Directory_UserAddress();
			$array[$key] = $this->recursiveHydrate($addressObject, $addressArray);
			
		}
		
		return $array;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getUsername()
	{
		$parts = explode('@', $this->primaryEmail);
		return $parts[0];
	}
	
	/**
	 * 
	 * @return string
	 */
	public function getDomain()
	{
		$parts = explode('@', $this->primaryEmail);
		return $parts[1];
	}
}
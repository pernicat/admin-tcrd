<?php
namespace TCRD;

use Google_Service_Directory_User as User;
use Exception;
use TCRD\Interfaces\UniqueIndexer;

/**
 * 
 * @author Tony Pernicano
 * 
 * Serves as a collection of domains.
 *
 */
class Domains implements UniqueIndexer
{
	/**
	 * 
	 * @var \TCRD\Domain[]
	 */
	public $domains;
	
	/**
	 * 
	 * @var \TCRD\Domain
	 */
	public $main;
	
	/**
	 * 
	 * @var multitype:multitype:User
	 */
	protected $uniqueIndex = array();
	
	/**
	 * 
	 * @var multitype:User
	 */
	protected $allUsers;
	
	/**
	 * 
	 * @param Domain $domain
	 * @return \TCRD\Domains
	 */
	public function addDomain(Domain $domain)
	{
		$this->domains[] = $domain;
		
		if (!$this->main) {
			$this->main = $domain;
		}
		
		$this->allUsers = null;
		$this->uniqueIndex = array();
		
		return $this;
	}
	
	/**
	 * 
	 * @param Domain $domain
	 * @return \TCRD\Domains
	 */
	public function setMainDomain(Domain $domain)
	{
		if (!$this->hasDomain($domain)) {
			$name = $domain->getName();
			throw new Exception("Domain '$name' can not be set as main " . 
								 "because it has not been added");
		}
		$this->main = $domain;
		return $this;
	}
	
	/**
	 *
	 * @param scalar $field
	 * @param scalar $value
	 * @return mixed
	 */
	public function getUnique($field, $value) 
	{
		// TODO
	}
	
	/**
	 *
	 * @param scalar $field
	 * @return array
	*/
	public function getUniqueIndex($field) 
	{
		if (!array_key_exists($field, $this->uniqueIndex)) {
			$this->uniqueIndex[$field] = $this->generateUniqueIndex($field);
		}

		return $this->uniqueIndex[$field];
	}
	
	/**
	 * 
	 * @param scalar $field
	 * @throws Exception
	 * @return multitype:User
	 */
	protected function generateUniqueIndex($field)
	{
		$index = array();
		
		foreach ($this->getAllUsers() as $user) {
			if (!is_object($user)) {
				$type = gettype($user);
				throw new Exception("\$user is a '$type' but should be an object");
			}
			
			if (!$user->__isset($field)) {
				$class = get_class($user);
				
				throw new Exception("'$field' not set for class '$class'");
			}
			
			$index[$user->__get($field)] = $user;
		}
		ksort($index);
		return $index;
	}
	
	/**
	 * 
	 * @return multitype:User
	 */
	public function getAllUsers()
	{
		if (!$this->allUsers) {
			$this->allUsers = array();
			
			foreach ($this->domains as $domain) {
				$users = $domain->getUsers();
				
				$this->allUsers = array_merge(
						$this->allUsers, 
						$users->toArray());
			}
		}
		return $this->allUsers;
	}
	
	/**
	 * 
	 * @param Domain|string $domain
	 * @throws Exception
	 * @return boolean
	 */
	public function hasDomain($domain)
	{
		if ($domain instanceof Domain) {
			$domain = $domain->getName();
		}
		
		if (!is_string($domain)) {
			$type = gettype($domain);
			throw new Exception("\$domain must be string or Domain, $type given");
		}
		
		/* @var $value Domain */
		foreach ($this->domains as $value) {
			if ($value->getName() === $domain) {
				return true;
			}
		}
		
		return false;
	}
}
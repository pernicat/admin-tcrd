<?php
namespace TCRD;

/**
 * 
 * @author Tony Pernicano
 * 
 * Serves as a collection of domains.
 *
 */
class Domains implements Interfaces\UniqueIndexer
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
	 * @var array
	 */
	public $unigueIndex = array();
	
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
			throw new Exception("Domain '$name' can not be set as main ". 
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
		// TODO
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
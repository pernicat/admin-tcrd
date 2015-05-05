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
		// TODO error checking
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
}
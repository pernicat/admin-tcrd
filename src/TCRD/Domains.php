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
	public $domains;
	
	public $main;
	
	public $unigueIndex = array();
	
	public function addDomain(Domain $domain)
	{
		if (!$main) {
			$this->main = $domain;
		}
		$this->domains[] = $domain;
		return $this;
	}
	
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
	public function getunique($field, $value) 
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
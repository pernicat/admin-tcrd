<?php
namespace TCRD;

use TCRD\Worksheet\Roster;


class App
{
	/**
	 * 
	 * @var array
	 */
	protected $domains = array();
	
	/**
	 * 
	 * @var Roster
	 */
	protected $roster;
	
	/**
	 * 
	 * @var array
	 */
	public $exempt = array();
	
	/**
	 * 
	 * @param Roster $roster
	 * @param array $domains
	 */
	public function __construct(Roster $roster, $domains)
	{
		$this->roster = $roster;
		
		if (!is_array($domains)) {
			$domains = array($domains);
		}
		
		$this->domains = $domains;
	}
	
	/**
	 * returns an arry of deactivated users
	 * @return array:
	 */
	public function getRemoveList()
	{
		$removed = array();
		foreach ($this->domains as $domain) {
			$removed = array_merge($removed, $this->getRemoveDomain($domain));
		}
		return $removed;
	}
	
	/**
	 * 
	 * @param Domain $domain
	 * @return Ambigous <multitype:, \TCRD\Google_Service_Directory_User>
	 */
	protected function getRemoveDomain(Domain $domain)
	{
		$users = $domain->getActiveUsers();
		
		$removeList = array();
		
		/* @var $user Google_Service_Directory_User */
		foreach ($users as $user) {
			$email = $user->getPrimaryEmail();
			
			if (in_array($email, $this->exempt)) {
				continue;
			}
			
			if ($this->roster->findEmail($email)) {
				continue;
			}
		
			if ($user->suspended) {
				continue;
			}
		
			$removeList[] = $user;
		
		}
		
		return $removeList;
	}
	
}
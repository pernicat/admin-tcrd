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
		
		/* @var $domain Domain */
		foreach ($domains as $domain) {
			$this->addDomain($domain);
		}
	}
	
	/**
	 * 
	 * @param Domain $domain
	 * @return App
	 */
	public function addDomain(Domain $domain)
	{
		$name = $domain->getName();
		$this->domains[$name] = $domain;
		return this;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return multitype:Domain|boolean
	 */
	public function getDomain($name)
	{
		if (isset($this->domains[$name])) {
			return $this->domains[$name];
		}
		return false;
	}
	
	/**
	 * returns an arry of deactivated users
	 * @return array:
	 */
	public function cleanUsers()
	{
		$removed = array();
		foreach ($this->domains as $domain) {
			$removed = array_merge($removed, $this->cleanDomainUsers($domain));
		}
		return $removed;
	}
	
	/**
	 * 
	 * @param \Google_Service_Directory_User $user
	 * @throws \Exception
	 * @return Ambigous <boolean, multitype:\TCRD\Domain >
	 */
	public function getUserDomain(\Google_Service_Directory_User $user) {
		$email = $user->getPrimaryEmail();
		$emailParts = explode('@', $email);
			
		$domain = $emailParts[1];
		
		if (!$domain) {
			throw new \Exception("could not find domain part of $email\n");
		}
		
		return $this->getDomain($domain);
	}
	
	/**
	 * 
	 * @param \Google_Service_Directory_User $user
	 */
	public function updateDomainUser(\Google_Service_Directory_User $user)
	{
		$domain = $this->getUserDomain($user);
		$directory = $domain->getDirectory();
		$email = $user->getPrimaryEmail();
		
		return $directory->users->update($email, $user);
	}
	
	/**
	 * 
	 * @param Domain $domain
	 * @return Ambigous <multitype:, \Google_Service_Directory_User>
	 */
	protected function cleanDomainUsers(Domain $domain)
	{
		
		$users = $domain->getActiveUsers();
		
		$removeList = array();
		
		/* @var $user \Google_Service_Directory_User */
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
			
			// Suspends the user
			$user->setSuspended(true);
			$domain->getDirectory()->users->update($email, $user);
			
			$removeList[] = $user;
		}
		
		return $removeList;
	}
	
	/**
	 * 
	 * @return multitype:\Google_Service_Directory_User
	 */
	public function findMissmatchUsers() 
	{
		$users = $this->getAllDomainUsers();
		
		$list = array();
		
		/* @var $user \Google_Service_Directory_User */
		foreach ($users as $user) {
			
			$email = $user->getPrimaryEmail();
			if ($this->roster->findEmail($email)) {
				continue;
			}
			
			$emailParts = explode('@', $email);
			
			$username = $emailParts[0];
			if (!$this->roster->findUsername($username)) {
				continue;
			}
			
			$list[] = $user;
		}
		return $list;
	}
	
	
	public function listUsers($params)
	{
		
	}
	
	/**
	 * 
	 * @return multitype:\Google_Service_Directory_User
	 */
	public function getSuspendedUsers() 
	{
		$users = array();
		/* @var $domain Domain */
		foreach ($this->domains as $domain) {
			$domainUsers = $domain->getSuspendedUsers();
			// TODO find more efficient way of doing this
			foreach ($domainUsers as $user) {
				$users[] = $user;
			}
		}
		return $users;
	}
	
	/**
	 * 
	 * @return multitype:\Google_Service_Directory_User
	 */
	public function getAllDomainUsers()
	{
		$users = array();
		/* @var $domain Domain */
		foreach ($this->domains as $domain) {
			$domainUsers = $domain->getAllUsers();
			// TODO find more efficient way of doing this
			foreach ($domainUsers as $user) {
				$users[] = $user;
			}
		}
		return $users;
	}
	
}
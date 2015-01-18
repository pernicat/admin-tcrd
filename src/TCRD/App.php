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
	 * @var array
	 */
	protected $domainIndex = array();
	
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
		return $this;
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
			throw new \Exception("could not find domain part of $email");
		}
		
		return $this->getDomain($domain);
	}
	
	/**
	 * 
	 * @param string $name
	 * @return Ambigous <\Google_Service_Directory_User>|boolean
	 */
	public function findUsername($name)
	{
		$index = $this->getDomainUsernameIndex();
		
		if (isset($index[$name])) {
			return $index[$name];
		}
		
		return false;
	}
	
	/**
	 * 
	 * @return multitype:\Google_Service_Directory_User
	 */
	public function getDomainUsernameIndex() {
		if (!isset($this->domainIndex['username'])) {
			$this->domainIndex['username'] = array();
			
			$users = $this->listUsers();
			
			/* @var $user \Google_Service_Directory_User */
			foreach ($users as $user) {
				$email = $user->getPrimaryEmail();
				$emailParts = explode('@', $email);
				$username = $emailParts[0];
					
				if (isset($this->domainIndex['username'][$username])) {
					continue;
				}
				
				$this->domainIndex['username'][$username] = $user;
			}
		}
		return $this->domainIndex['username'];
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
	 * returns an arry of deactivated users
	 * @return array:
	 */
	public function listRemovedUsers()
	{
		$users = $this->listUsers(array('query' => 'isSuspended=false'));
		
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
				
			$removeList[] = $user;
		}
		
		return $removeList;
	}
	
	public function listUnsuspendedUsers()
	{
		$users = $this->listUsers(array('query' => 'isSuspended=true'));
		
		$list = array();
		
		/* @var $user \Google_Service_Directory_User */
		foreach ($users as $user) {
			$email = $user->getPrimaryEmail();
			
			if ($this->roster->findEmail($email)) {
				$list[] = $user;
			}
		}
		return $list;
	}
	
	/**
	 * 
	 * @return multitype:\Google_Service_Directory_User
	 */
	public function listMissmatchUsers() 
	{
		$users = $this->listUsers();
		
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
	
	/**
	 * 
	 * @param array $params
	 * @return multitype:\Google_Service_Directory_User
	 */
	public function listUsers($params = array())
	{
		$users = array();
		/* @var $domain Domain */
		foreach ($this->domains as $domain) {
			$domainUsers = $domain->listUsers($params);
			// TODO find more efficient way of doing this
			foreach ($domainUsers as $user) {
				$users[] = $user;
			}
		}
		return $users;
	}
	
}
<?php
namespace TCRD;

use TCRD\Worksheet\Roster;
use TCRD\Worksheet\WorksheetContainer;


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
	public $roster;
	
	/**
	 * 
	 * @var WorksheetContainer
	 */
	public $positions;
	
	/**
	 * 
	 * @var Domain
	 */
	public $mainDomain;
	
	/**
	 * 
	 * @var array
	 */
	public $exempt = array();
	
	/**
	 * 
	 * @var Filter\Data\DataFilter
	 */
	public $positionsFilter;
	
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
	 * @return \TCRD\Domain
	 */
	public function getMainDomain()
	{
		// TODO error checking
		return $this->mainDomain;
	}
	
	/**
	 * 
	 * @param array $values
	 * @throws \Exception
	 * @return array
	 */
	public function createUserValues($values)
	{
		if (!isset($values['givenname'])) {
			throw new \Exception("'givenname' must be set");
		}
		
		if (!isset($values['familyname'])) {
			throw new \Exception("'familyname' must be set");
		}
		
		if (!isset($values['username']) || !$values['username']) {
			$values['username'] = $this->generateUsername(
					$values['givenname'], 
					$values['familyname']);
		} else {
			$values['username'] =  $this->cleanName($values['username']);
		}
		
		$domain = $this->getNextDomain();
		
		$domainName = $domain->getName();
		
		$values['tcrde-mail'] = $values['username'] . '@' . $domainName;
		return $values;
	}
	
	/**
	 * 
	 * @param string $givenName
	 * @param string $familyName
	 * @return string
	 */
	public function generateUsername($givenName, $familyName)
	{
		$givenName = $this->cleanName($givenName);
		$familyName = $this->cleanName($familyName);
		
		return "$givenName.$familyName";
	}
	
	/**
	 * 
	 * @param string $name
	 * @return string
	 */
	public function cleanName($name)
	{
		return strtolower(str_replace(' ', '', $name));
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
	 * @param string $email
	 * @return \TCRD\Ambigous
	 */
	public function findEmail($email)
	{
		$username = Util::usernameFromEmail($email);
		return $this->findUsername($username);
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
				$username = Util::usernameFromEmail($email);
					
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
	
	/**
	 * 
	 * @return multitype:\Google_Service_Directory_User
	 */
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
			
			if (in_array($email, $this->exempt)) {
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
	 * @return multitype:\Google_Service_Directory_Group
	 */
	public function listNewPositions()
	{
		$listFeed = $this->positions->getListFeed();

		$results = array();
		
		/* @var $entry Google\Spreadsheet\ListEntry */
		foreach ($listFeed->getEntries() as $entry) {
			$values = $entry->getValues();
			
			if (!$this->mainDomain->getGroupUsername($values['email'])) {
				
				$group = new \Google_Service_Directory_Group();
				
				$email = $this->mainDomain->usernameToEmail($values['email']);
				
				$group->setEmail($email);
				$group->setName($values['name']);
				$group->setDescription($values['description']);
				
				$results[] = $group;
			}
		}
		return $results;
	}
	
	/**
	 * 
	 * @return multitype:array
	 */
	public function listRemoveUsersFromPositions()
	{
		$listFeed = $this->positions->getListFeed();
		
		$results = array();
		
		/* @var $entry Google\Spreadsheet\ListEntry */
		foreach ($listFeed->getEntries() as $entry) {
			$values = $entry->getValues();
			
			$groupKey = $this->mainDomain->usernameToEmail($values['email']);
			
			$members = Util::csvToArray($values['member']);
			$list = $this->roster->findMemberKeys($members);
			
			if (!$this->mainDomain->getGroup($groupKey)) {
				continue;
			}
			
			$excluded = $this->mainDomain->memberExclude($groupKey, $list);
			
			$results = array_merge($results, $excluded);
		}
		return $results;
	}
	
	/**
	 * 
	 * @return multitype:array
	 */
	public function listAddUsersToPositions()
	{
		$listFeed = $this->positions->getListFeed();
		
		$results = array();
		
		/* @var $entry Google\Spreadsheet\ListEntry */
		foreach ($listFeed->getEntries() as $entry) {
			$values = $entry->getValues();
				
			$groupKey = $this->mainDomain->usernameToEmail($values['email']);
				
			$members = Util::csvToArray($values['member']);
			$list = $this->roster->findMemberKeys($members);
				
			if (!$this->mainDomain->getGroup($groupKey)) {
				continue;
			}
				
			$excluded = $this->mainDomain->memberInclude($groupKey, $list);
				
			$results = array_merge($results, $excluded);
		}
		return $results;
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
	
	
	public function getNextDomain()
	{
		$most = 0;
		$domain = false;
		/* @var $domain \TCRD\Domain */
		foreach ($this->domains as $domain) {
			$vacancy = $domain->getVacancy();
			if ($vacancy > $most) {
				$most = $vacancy;
				$domain = $domain;
			}
		}
		return $domain;
	}
	
	
	/**
	 * @return \Google_Service_Directory
	 */
	public function getDirectory()
	{
		$domain = $this->getMainDomain();
		return $domain->getDirectory();
	}
	
	/**
	 * @todo remove this?
	 * @param unknown $params
	 */
	public function listGroups($params = array())
	{
		$domain = $this->getMainDomain();		
		return $domain->listGroups($params);
	}
	
}
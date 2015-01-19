<?php
namespace TCRD;

class Domain implements \Countable
{
	
	const DOMAIN_LIMIT = 50;
	
	protected $count;
	
	/**
	 * 
	 * @var number
	 */
	public $userBuffer = 5;
	
	/**
	 * 
	 * @var ClientContainer
	 */
	protected $clientContainer;
	
	/**
	 * 
	 * @var array
	 */
	protected $specs;
	
	/**
	 * 
	 * @var \Google_Service_Directory
	 */
	protected $directory;
	
	/**
	 * 
	 * @var array
	 */
	protected $goupIndex;
	
	/**
	 * 
	 * @var array
	 */
	protected $membersIndex = array();
	
	/**
	 * 
	 * @var array
	 */
	public $ignore = array();
	
	/**
	 * 
	 * @param ClientContainer $clientContainer
	 * @param array|string $specs
	 */
	public function __construct(ClientContainer $clientContainer, $specs) 
	{
		$this->clientContainer = $clientContainer;
		
		if (is_string($specs)) {
			$specs = array('name' => $specs);
		}
		
		$this->setSpecs($specs);
	}
	
	protected function setSpecs($specs) {
		if (!isset($specs['name'])) {
			throw new \Exception("specs must specify a name");
		}
		
		$this->specs = $specs;
	}
	
	/**
	 * @return \Google_Client
	 */
	public function getClient()
	{
		return $this->clientContainer->getClient();
	}
	
	/**
	 * 
	 * @return \Google_Service_Directory
	 */
	public function getDirectory()
	{
		if (null == $this->directory) {
			$client = $this->getClient();
			$this->directory = new \Google_Service_Directory($client);
		}
		return $this->directory;
	}
	
	/**
	 * 
	 * @return string:
	 */
	public function getName()
	{
		// TODO error checking
		return $this->specs['name'];
	}
	
	/**
	 * 
	 * @param array $parms
	 * @return Google_Service_Directory_Users
	 */
	public function listUsers($params = array())
	{
		if (!is_array($params)) {
			throw new \Exception("\$params must be an array.");
		}
		$params['domain'] = $this->getName();
		$directory = $this->getDirectory();
		$users =  $directory->users->listUsers($params);
		
		if (1 === count($params)) {
			$this->count = count($users);
		}
		
		return $users;
	}
	
	/**
	 * 
	 * @param array $params
	 * @throws \Exception
	 * @return Google_Service_Directory_Groups
	 */
	public function listGroups($params = array())
	{
		if (!is_array($params)) {
			throw new \Exception("\$params must be an array.");
		}
		$params['domain'] = $this->getName();
		$directory = $this->getDirectory();
		return $directory->groups->listGroups($params);
	}
	
	/**
	 * 
	 * @param string $groupKey
	 * @param string $email
	 * @return Ambigous <Google_Service_Directory_Member>|boolean
	 */
	public function getMember($groupKey, $email)
	{
		$index = $this->getMembersIndex($groupKey);
		if (isset($index[$email])) {
			return $index[$email];
		}
		return false;
	}
	
	/**
	 * 
	 * @param string $groupKey
	 * @return multitype:\Google_Service_Directory_Member
	 */
	public function getMembersIndex($groupKey)
	{
		if (!isset($this->membersIndex[$groupKey])) {
			
			$this->membersIndex[$groupKey] = array();
			
			$directory = $this->getDirectory();
			$members = $directory->members->listMembers($groupKey);
			
			/* @var $member \Google_Service_Directory_Member */
			foreach ($members as $member) {
				$this->membersIndex[$groupKey][$member->email] = $member;
			}
			
		}
		return $this->membersIndex[$groupKey];
	}
	
	/**
	 * 
	 * @param string $name
	 * @return Ambigous <\Google_Service_Directory_Group>|boolean
	 */
	public function getGroup($name)
	{
		$index = $this->getGroupIndex();
		
		if (isset($index[$name])) {
			return $index[$name];
		}
		return false;
	}
	
	/**
	 * 
	 * @return multitype:\Google_Service_Directory_Group
	 */
	public function getGroupIndex()
	{
		if (null == $this->goupIndex) {
			$this->goupIndex =  array();
			
			$groups = $this->listGroups();
			
			/* @var $group Google_Service_Directory_Group */
			foreach ($groups as $group) {

				$email = $group->getEmail();
				
				$this->goupIndex[$email] = $group;
			
			}
		}
		return $this->goupIndex;
	}
	
	/**
	 * Returnes members in the list that are not in the group.
	 *
	 * @param string $groupKey
	 * @param string $list An array containing memberKeys
	 * @return multitype:array containing 'groupKey' and 'memberKey'
	 */
	public function memberInclude($groupKey, $list)
	{		
		$results = array();
		
		foreach ($list as $email) {
			if (!$this->getMember($groupKey, $email)) {
				$results[] = array(
						'groupKey' => $groupKey,
						'memberKey' => $email);
			}
		}
		return $results;
	}
	
	/**
	 * Returnes members in group that are not in the list.
	 * 
	 * @param string $groupKey
	 * @param string $list An array containing memberKeys
	 * @return multitype:array containing 'groupKey' and 'memberKey'
	 */
	public function memberExclude($groupKey, $list)
	{
		$index = $this->getMembersIndex($groupKey);
		
		$results = array();
		
		/* @var $index \Google_Service_Directory_Member */
		foreach ($index as $member) {
			$email = $member->getEmail();
		
			if (!in_array($email, $list)) {
					
				if (in_array($email, $this->ignore)) {
					continue;
				}
					
				$results[] = array(
						'groupKey' => $groupKey,
						'memberKey' => $email);
			}
		}
		return $results;
	}
	
	/**
	 * 
	 * @return number
	 */
	public function count()
	{
		if (null === $this->count) {
			$this->count = count($this->listUsers());
		}
		return $this->count;
	}
	
	/**
	 * 
	 * @return number
	 */
	public function getVacancy()
	{
		$count = count($this);
		$vacancy = self::DOMAIN_LIMIT - $this->userBuffer - $count;
		
		if (0 > $vacancy) {
			return 0;
		}
		
		return $vacancy;
	}
	
	
	
}
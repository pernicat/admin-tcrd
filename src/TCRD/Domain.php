<?php
namespace TCRD;

use TCRD\Wrapper\CollectionWrapper;
class Domain implements \Countable
{
	
	const DEFAULT_USER_LIMIT = 50;
	const DEFAULT_USER_BUFFER = 5;
	
	/**
	 * 
	 * @var number
	 */
	protected $userLimit = self::DEFAULT_USER_LIMIT;
	
	/**
	 *
	 * @var number
	 */
	public $userBuffer = self::DEFAULT_USER_BUFFER;
	
	/**
	 * 
	 * @var \Google_Service_Directory
	 */
	protected $directory;
	
	/**
	 * 
	 * @var string
	 */
	protected $name;
	
	/**
	 * 
	 * @var \TCRD\Wrapper\CollectionWrapper
	 */
	protected $users;
	
	/**
	 * 
	 * @var string
	 */
	protected $userClass = '\\TCRD\\Wrapper\\UserWrapper';
	
	/**
	 * 
	 * @var \TCRD\Wrapper\CollectionWrapper
	 */
	protected $groups;
	
	/**
	 * @var string
	 */
	protected $groupClass = '\\TCRD\\Wrapper\\GroupWrapper';
	
	/**
	 * 
	 * @var array
	 */
	public $ignore = array();
	
	
	/**
	 * 
	 * @param \Google_Service_Directory $directory
	 * @param string $name
	 */
	public function __construct(\Google_Service_Directory $directory, $name) 
	{
		$this->directory = $directory;
		$this->name = $name;
	}
	
	/**
	 * @todo
	 * @return string:
	 */
	public function getName()
	{
		// TODO error checking
		return $this->name;
	}
	
	/**
	 * 
	 * @param array $params
	 * @throws \Exception
	 * @return \TCRD\Wrapper\CollectionWrapper
	 */
	public function listUsers($params = array())
	{
		if (!is_array($params)) {
			throw new \Exception("\$params must be an array.");
		}
		$params['domain'] = $this->getName();
		$users =  $this->directory->users->listUsers($params);
		
		if (1 === count($params)) {
			$this->count = count($users);
		}
		
		$userWrapper = new CollectionWrapper($users);
		$userWrapper->setItemClass($this->userClass);
		
		// TODO cache users if no $params
		
		return $userWrapper;
	}
	
	/**
	 * 
	 * @return \TCRD\Wrapper\CollectionWrapper
	 */
	public function getUsers()
	{
		if (!$this->users) {
			$this->users = $this->listUsers();
		}
		return $this->users;
	}
	
	/**
	 * @todo
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
		
		$groups = $directory->groups->listGroups($params);
		
		$groupWrapper = new CollectionWrapper($groups);
		$groupWrapper->setItemClass($this->groupClass);
		// TODO set group directory
		
		// TODO set groups to this->groups if params == 1
		return $groupWrapper;
	}
	
	/**
	 * 
	 * @return \TCRD\Wrapper\CollectionWrapper
	 */
	public function getGroups()
	{
		if (!$this->groups) {
			$this->groups = $this->listGroups();
		}
		return $this->groups;
	}
	
	/**
	 * @todo
	 * @return number
	 */
	public function count()
	{
		return count($this->getUsers());
	}
	
	/**
	 * @todo
	 * @return number
	 */
	public function getVacancy()
	{
		$vacancy = $this->userLimit - $this->userBuffer - count($this);
		
		if (0 > $vacancy) {
			return 0;
		}
		
		return $vacancy;
	}
	
	
	
}
<?php
namespace TCRD;

class Domain
{
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
	 * @param ClientContainer $clientContainer
	 * @param array $specs
	 */
	public function __construct(ClientContainer $clientContainer, $specs = null) 
	{
		$this->clientContainer = $clientContainer;
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
	 * @return \Google_Service_Directory_Users 
	 */
	public function getActiveUsers()
	{
		$directory = $this->getDirectory();
		$users = $directory->users->listUsers(array(
				'domain' => $this->getName(),
				'query' => 'isSuspended=false'
		));
		return $users;
	}
	
	/**
	 * 
	 * @return \Google_Service_Directory_Users 
	 */
	public function getSuspendedUsers()
	{
		$directory = $this->getDirectory();
		$users = $directory->users->listUsers(array(
				'domain' => $this->getName(),
				'query' => 'isSuspended=true'
		));
		return $users;
	}
	
	/**
	 * 
	 * @return Google_Service_Directory_Users
	 */
	public function getAllUsers()
	{
		$directory = $this->getDirectory();
		$users = $directory->users->listUsers(array(
				'domain' => $this->getName()
		));
		return $users;
	}
	
	public function listUsers($parms = array())
	{
		$parms['domain'] = $this->getName();
		$directory = $this->getDirectory();
		return $directory->users->listUsers($parms);
		
	}
}
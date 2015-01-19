<?php
namespace TCRD;

class Process
{
	/**
	 * 
	 * @var App
	 */
	protected $app;
	
	/**
	 * 
	 * @param App $app
	 */
	public function __construct(App $app)
	{
		$this->app = $app;
	}
	
	/**
	 * 
	 * @return multitype:
	 * @return Process
	 */
	public function run()
	{
		$this->removeUsers()
			 ->unremoveUsers()
			 ->checkMismatches();
		
		return $this;
	}
	
	/**
	 * checks roster for missing users to suspend and disable.
	 * @return Process
	 */
	public function removeUsers()
	{
		$users = $this->app->listRemovedUsers();
		
		/* @var $user \Google_Service_Directory_User */
		foreach ($users as $user) {
			$this->log($user->getPrimaryEmail() . " -> removing");
			$user->setSuspended(true);
			
			$this->app->updateDomainUser($user);
		}
		return $this;
	}
	
	/**
	 * reactivates users that may have been wrongfully removed
	 * @return Process
	 */
	public function unremoveUsers()
	{
		$users = $app->listUnsuspendedUsers();
		
		/* @var $user \Google_Service_Directory_User */
		foreach ($users as $user) {
			$this->log($user->getPrimaryEmail() . " -> unremoved");
			$user->setSuspended(false);
				
			$this->app->updateDomainUser($user);
		}
		return $this;
	}
	
	/**
	 * Logs any missmatches between e-mail addresses
	 * @return Process
	 */
	public function checkMismatches()
	{
		$users = $app->findMissmatchUsers();
		
		/* @var $user \Google_Service_Directory_User */
		foreach ($users as $user) {
			$this->log($user->getPrimaryEmail() . " -> is mismatched, please check");
		}
		return $this;
	}
	
	public function newPositions()
	{
		$positions = $this->app->listNewPositions();
		$directory = $this->app->getDirectory();
		
		/* @var $group \Google_Service_Directory_Group */
		foreach ($positions as $group) {
			$directory->groups->insert($group);
			
			$msg = $group->getName() . ":" . 
				   $group->getEmail() . " -> position created";
			$this->log($msg);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @param string $string
	 */
	public function log($string)
	{
		// TODO store to array
		print $string . "\n";
	}
	
}
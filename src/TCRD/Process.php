<?php
namespace TCRD;

class Process
{
	/**
	 * 
	 * @var App
	 */
	public $app;
	
	/**
	 * 
	 * @var \TCRD\Validator\UsernameValidator
	 */
	public $usernameValidator;
	
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
		if ($this->isDebug()) {
			$this->log("running in DEBUG mode");
		}
		
		
		// TODO add info to empty groups
		// TODO filter postions
		// TODO filter users
		
		
		$this->removeUsers()
			 ->unremoveUsers()
			 // TODO new users
			 // TODO email new users
			 // TODO verify all users exist
			 ->checkMismatches()
			 ->validatePositionMembers()
			 ->newPositions()
			 ->removeUsersFromPositions()
			 ->addusersToPositions()
			 // TODO remove users from groups
			 // TODO add users to groups
			 
		     
		     // TODO make sure all users are in broadcast
		     // TODO add share to all groups
		     ;
		
		// TODO e-mail it
		
		return $this;
	}
	
	/**
	 * filters all of the positions data and cleans it if neccesary.
	 * @return \TCRD\Process
	 */
	public function filterPositions()
	{
		$listFeed = $this->app->positions->getListFeed();
		
		$filter = $this->app->positionsFilter;
		
		/* @var $entry \Google\Spreadsheet\ListEntry */
		foreach ($listFeed->getEntries() as $entry) {
			$values = $entry->getValues();
			
			$filter->setValue($values);
			$filter->filter();
			
			if ($filter->hasChanged()) {
				array_map(array($this, 'log'), $filter->getMessages());
			}
			
			if (!$this->isDebug()) {
				$entry->update($filter->getValue());
			}
			
		}
		
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
			
			if (!$this->isDebug()) {
				$this->app->updateDomainUser($user);
			}
		}
		return $this;
	}
	
	/**
	 * reactivates users that may have been wrongfully removed
	 * @return Process
	 */
	public function unremoveUsers()
	{
		$users = $this->app->listUnsuspendedUsers();
		
		/* @var $user \Google_Service_Directory_User */
		foreach ($users as $user) {
			$this->log($user->getPrimaryEmail() . " -> unremoved");
			$user->setSuspended(false);
			
			if (!$this->isDebug()) {
				$this->app->updateDomainUser($user);
			}
		}
		return $this;
	}
	
	/**
	 * Logs any missmatches between e-mail addresses
	 * @return Process
	 */
	public function checkMismatches()
	{
		$users = $this->app->listMissmatchUsers();
		
		/* @var $user \Google_Service_Directory_User */
		foreach ($users as $user) {
			$this->log($user->getPrimaryEmail() . " -> is mismatched, please check");
		}
		return $this;
	}
	
	/**
	 * 
	 * @return \TCRD\Process
	 */
	public function createUsers()
	{
		$entries = $this->app->roster->listNoEmail();
		
		// TODO dependency injection for this
		$validator = new \TCRD\Validator\NewUserValidator();
		
		/* @var $entry \Google\Spreadsheet\ListEntry */
		foreach ($entries as $entry) {
			$values = $entry->getValues();
			$validator->setValue($values);
			
			if (!$validator->isValid()) {
				$errors = $validator->getErrors();
				// TODO fix this
				$this->log("Creat User Error: " . $errors[0]);
				continue;
			}
			
			
			$values = $this->app->createUserValues($values);
			
			$givenName = $values['givenname'];
			$familyName = $values['familyname'];
			
			$this->log("adding $givenName $familyName");
			
			//print_r($values);
			
			$entry->update($values);
		}
		return $this;
	}
	
	/**
	 *
	 * @return \TCRD\Process
	 */
	public function validatePositionMembers()
	{
		$listFeed = $this->app->positions->getListFeed();
	
		$results = array();
	
		$members = array();
		foreach ($listFeed->getEntries() as $entry) {
			$values = $entry->getValues();
			$member = $values['member'];
			$split = Util::csvToArray($member);
			$members = array_merge($members, $split);
		}
		
		
		/* @var $entry Google\Spreadsheet\ListEntry */
		foreach ($members as $member) {
				
			$this->usernameValidator->setValue($member);
			if ($this->usernameValidator->isValid()) {
				continue;
			}
				
			$errors = $this->usernameValidator->getErrors();
			foreach ($errors as $error) {
				$this->log("Position Error: Member " . $error);
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @return \TCRD\Process
	 */
	public function newPositions()
	{
		$positions = $this->app->listNewPositions();
		$directory = $this->app->getDirectory();
		
		/* @var $group \Google_Service_Directory_Group */
		foreach ($positions as $group) {
			
			if (!$this->isDebug()) {
				$directory->groups->insert($group);
			}
			
			$msg = $group->getName() . ":" . 
				   $group->getEmail() . " -> position created";
			$this->log($msg);
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @return \TCRD\Process
	 */
	public function removeUsersFromPositions()
	{
		$keys = $this->app->listRemoveUsersFromPositions();
		foreach ($keys as $key) {
			$this->log($key['groupKey'] . " remove " . $key['memberKey']);
			
			if (!$this->isDebug()) {
				// TODO
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @return \TCRD\Process
	 */
	public function addUsersToPositions()
	{
		$keys = $this->app->listAddUsersToPositions();
		foreach ($keys as $key) {
			$this->log($key['groupKey'] . " add " . $key['memberKey']);
				
			if (!$this->isDebug()) {
				$member = new \Google_Service_Directory_Member();
				$member->setEmail($key['memberKey']);
	
				$directory = $this->app->mainDomain->getDirectory();
				$directory->members->insert($key['groupKey'], $member);
			}
		}
		return $this;
	}
	
	/**
	 * 
	 * @return \TCRD\Process
	 */
	public function addShareToAll()
	{
		// TODO implement
		return $this;
	}
	
	
	
	/**
	 * 
	 * @return boolean
	 */
	public function isDebug()
	{
		return (defined('DEBUG') && constant('DEBUG'));
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
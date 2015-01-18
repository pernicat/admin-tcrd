<?php
namespace TCRD\Worksheet;

class Roster extends WorksheetContainer
{

	/**
	 * 
	 * @var array
	 */
	protected $emailIndex;
	
	/**
	 *
	 * @var array
	 */
	protected $usernameIndex;
	
	/**
	 * 
	 * @param string $email
	 * @return \TCRD\Worksheet\Ambigous|boolean
	 */
	public function findEmail($email)
	{
		$index = $this->getEmailIndex();
		
		if (isset($index[$email])) {
			return $index[$email];
		}
		return false;
	}
	
	/**
	 *
	 * @param string $username
	 * @return \TCRD\Worksheet\Ambigous|boolean
	 */
	public function findUsername($username)
	{
		$index = $this->getUsernameIndex();
	
		if (isset($index[$username])) {
			return $index[$username];
		}
		return false;
	}
	
	/**
	 * special e-mail index due to capitalization issue
	 * 
	 * @return Ambigous <multitype:, \TCRD\Worksheet\Google\Spreadsheet\ListEntry>
	 */
	public function getEmailIndex() 
	{
		if (null == $this->emailIndex) {
			$this->emailIndex = array();
			
			$listFeed = $this->getListFeed();
			
			/* @var $entry Google\Spreadsheet\ListEntry */
			foreach ($listFeed->getEntries() as $entry) {
				$values = $entry->getValues();
				$key = trim(strtolower($values['tcrde-mail']));
				
				// TODO some error checking
				$this->emailIndex[$key] = $entry;
			}
		}
		return $this->emailIndex;
		
	}
	
	/**
	 * special username index due to capitalization issue
	 *
	 * @return Ambigous <multitype:, \TCRD\Worksheet\Google\Spreadsheet\ListEntry>
	 */
	public function getUsernameIndex()
	{
		if (null == $this->usernameIndex) {
			$this->usernameIndex = array();
				
			$listFeed = $this->getListFeed();
				
			/* @var $entry Google\Spreadsheet\ListEntry */
			foreach ($listFeed->getEntries() as $entry) {
				$values = $entry->getValues();
				$key = trim(strtolower($values['username']));
	
				// TODO some error checking
				$this->usernameIndex[$key] = $entry;
			}
		}
		return $this->usernameIndex;
	
	}
	

}
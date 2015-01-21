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
	 * @return \Google\Spreadsheet\ListEntry|boolean
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
	 * @return multitype:\Google\Spreadsheet\ListEntry
	 */
	public function listNoEmail()
	{
		$listFeed = $this->getListFeed();
		$result = array();	
		
		/* @var $entry \Google\Spreadsheet\ListEntry */
		foreach ($listFeed->getEntries() as $entry) {
			$values = $entry->getValues();
			$key = trim(strtolower($values['tcrde-mail']));
		
			if (!$key) {
				$result[] = $entry;
			}
		}
		
		return $result;
	}
	
	/**
	 *
	 * @param string $username
	 * @return \Google\Spreadsheet\ListEntry|boolean
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
	 * 
	 * @param array $members
	 * @return multitype:string
	 */
	public function findMemberkeys($members)
	{
		$results = array();
		foreach ($members as $username) {
			if ($email = $this->findMemberKey($username)) {
				$results[] = $email;
			}
		}
		return $results;
	}
	
	/**
	 * 
	 * @param string $username
	 * @return \Google\Spreadsheet\ListEntry|boolean
	 */
	public function findMemberKey($username)
	{
		if ($entry = $this->findUsername($username)) {
			$values = $entry->getValues();
			return $values['tcrde-mail'];
		}
		return false;
	}
	
	/**
	 * special e-mail index due to capitalization issue
	 * 
	 * @return Ambigous <multitype:, \Google\Spreadsheet\ListEntry>
	 */
	public function getEmailIndex() 
	{
		return $this->getIndex('tcrde-mail');
	}
	
	/**
	 * special username index due to capitalization issue
	 *
	 * @return Ambigous <multitype:, \Google\Spreadsheet\ListEntry>
	 */
	public function getUsernameIndex()
	{
		if (null == $this->usernameIndex) {
			$this->usernameIndex = array();
				
			$emailIndex = $this->getEmailIndex();
				
			/* @var $entry Google\Spreadsheet\ListEntry */
			foreach ($emailIndex as $key => $values) {
				$this->usernameIndex[\TCRD\Util::usernameFromEmail($key)] = $values;
			}
		}
		return $this->usernameIndex;
	}
	
	/**
	 * 
	 * @param string $name
	 * @return string|boolean
	 */
	public function findClosestUsername($name, $limit = -1)
	{
		$usernames = $this->getUsernameIndex();
		
		$shortest = -1;
		foreach ($usernames as $key => $value) {
			$lev = levenshtein($name, $key);
		
			if ($lev == 0) {
				$closest = $key;
				$shortest = 0;
		
				break;
			}
		
			if ($lev <= $shortest || $shortest < 0) {
				$closest  = $key;
				$shortest = $lev;
			}
		}
		
		if (0 <= $limit && $shortest > $limit) {
			return false;
		}
		
		return $closest;
	}
	

}
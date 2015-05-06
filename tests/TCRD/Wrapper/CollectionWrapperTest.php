<?php

namespace TCRD\Tests\Wrapper;

use PHPUnit_Framework_TestCase as TestCase;
use Google_Service_Directory_Users as Users;
use Google_Service_Directory_User as User;
use TCRD\Wrapper\CollectionWrapper;

class CollectionWrapperTest extends TestCase
{
	/**
	 * @dataProvider usersProvider
	 * @param Users $users
	 * @param Array $emails
	 */
	public function testSetupUsers(Users $users, Array $emails)
	{
		$wrappedUsers = new CollectionWrapper($users);
		$wrappedUsers->setItemClass('TCRD\Wrapper\UserWrapper');
		
		$this->assertEquals(count($emails), count($wrappedUsers));
		
		// expanded foreach for testing.
		$wrappedUsers->rewind();
		while ($wrappedUsers->valid()) {
			$key = $wrappedUsers->key();
			$user = $wrappedUsers->current();
			
			$this->assertInstanceOf('TCRD\Wrapper\UserWrapper', $user);
				
			$this->assertEquals($emails[$key], $user->primaryEmail);
			$this->assertEquals($emails[$key], $user['primaryEmail']);
			
			$wrappedUsers->next();
			
			$unique = $wrappedUsers->findUnique('primaryEmail', $emails[$key]);
			$this->assertEquals($emails[$key], $unique->primaryEmail);
		}
		
		$emailIndex = $wrappedUsers->getUniqueIndex('primaryEmail');

		$emailKeys = array_keys($emailIndex);
		
		$this->assertEquals($emails[0], $emailKeys[0]);
	}
	
	/**
	 * @dataProvider usersProvider
	 * @param Users $users
	 * @param Array $emails
	 */
	public function testToArray(Users $users, Array $emails)
	{
		$wrappedUsers = new CollectionWrapper($users);
		$wrappedUsers->setItemClass('TCRD\Wrapper\UserWrapper');
	
		$usersArray = $wrappedUsers->toArray();
		
		foreach ($usersArray as $user) {
			$this->assertInstanceOf('TCRD\Wrapper\UserWrapper', $user);
		}
	}
	
	/**
	 * 
	 * @return multitype:multitype:multitype:string  \Google_Service_Directory_Users
	 */
	public function usersProvider() 
	{
		$emails = array(
				'tony.pernicano@tcrollerderby.com',
				'john.deer@tcrollerderby.com',
				'taco.chris@tcrollerderby.com',
				'bob.loblaw@tcrollerderby.com',
				'kim.deal@tcrollerderby.com',
				'nora.dame@tcrollerderby.com'
		);
		
		$userArray = array();
		
		foreach ($emails as $key => $email) {
			$user = new User();
			$user->primaryEmail = $email;
				
			$userArray[] = $user;
		}
		
		$users = new Users(array('users' => $userArray));
		
		return array(array($users, $emails));
	}
	
}
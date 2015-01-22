<?php

class CollectionWrapperTest extends PHPUnit_Framework_TestCase
{
	public function testSetup()
	{
		$emails = $this->emailProvider();
		
		$userArray = array();
		
		foreach ($emails as $key => $email) {
			$user = new Google_Service_Directory_User();
			$user->primaryEmail = $email;
			
			$userArray[] = $user;
		}
		
		
		
		$users = new Google_Service_Directory_Users(array('users' => $userArray));
		
		
		$wrappedUsers = new TCRD\Wrapper\CollectionWrapper($users);
		$wrappedUsers->setItemClass('\\TCRD\\Wrapper\\UserWrapper');
		
		$this->assertEquals(count($emails), count($wrappedUsers));
		
		// expanded foreach for testing.
		$wrappedUsers->rewind();
		while ($wrappedUsers->valid()) {
			$key = $wrappedUsers->key();
			$user = $wrappedUsers->current();
			
			$this->assertInstanceOf('\\TCRD\\Wrapper\\UserWrapper', $user);
				
			$this->assertEquals($emails[$key], $user->primaryEmail);
			$this->assertEquals($emails[$key], $user['primaryEmail']);
			
			$wrappedUsers->next();
		}
		
		
		$emailIndex = $wrappedUsers->getUniqueIndex('primaryEmail');

		$emailKeys = array_keys($emailIndex);
		
		//print_r($emails);
		
		$this->assertEquals($emails[0], $emailKeys[0]);
		
		$tacoChris = $wrappedUsers->findUnique('primaryEmail', 'taco.chris@tcrollerderby.com');
		
		$this->assertEquals('taco.chris@tcrollerderby.com', $tacoChris->primaryEmail);
	}
	
	public function emailProvider() {
		return array(
				'tony.pernicano@tcrollerderby.com',
				'john.deer@tcrollerderby.com',
				'taco.chris@tcrollerderby.com',
				'bob.loblaw@tcrollerderby.com',
				'kim.deal@tcrollerderby.com',
				'nora.dame@tcrollerderby.com'
		);
	}
}
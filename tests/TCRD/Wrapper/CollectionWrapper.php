<?php

class CollectionWrapperTest extends PHPUnit_Framework_TestCase
{
	public function testSetup()
	{
		$user = new Google_Service_Directory_User();
		$user->primaryEmail = 'tony.pernicano@tcrollerderby.com';
		
		
		
		$collection = new Google_Collection();
		
		$collection[] = $user;
		
		$this->assertEquals($collection[0]->primaryEmail, $user->primaryEmail);
		
		
		
	}
}
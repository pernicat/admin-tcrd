<?php

class UserWrapperTest extends PHPUnit_Framework_TestCase
{
	public function testSetup() 
	{

		$user = new Google_Service_Directory_User();
		
		$user->primaryEmail = 'tony.pernicano@tcrollerderby.com';
		
		$user->addresses = array();
		$user->addresses[0] = new Google_Service_Directory_UserAddress();
		$user->addresses[0]->type = 'Home';
		$user->addresses[0]->customType = '';
		$user->addresses[0]->streetAddress = '1234 Bad Rd';
		$user->addresses[0]->locality = 'Detroit';
		$user->addresses[0]->region = 'MI';
		$user->addresses[0]->postalCode = '49675';
		
		$user->name = new Google_Service_Directory_UserName();
		$user->name->givenName = 'Tony';
		$user->name->familyName = 'Pernicano';
		$user->name->fullName = 'Tony Pernicano';
		
		
		$wrapped = new TCRD\Wrapper\UserWrapper($user);
		
		$this->assertEquals($wrapped->getName(), $user->name);
		
		$values = $wrapped->toArray();
		print_r($wrapped->toArray());
		
		$this->assertEquals($user->addresses[0]->type, $values['addresses'][0]['type'] );
		
		print_r(get_class($wrapped->getObject()));
	}
	
	public function userProvider()
	{
		
	}
}
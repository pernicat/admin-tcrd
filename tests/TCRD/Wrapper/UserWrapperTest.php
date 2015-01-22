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
		//print_r($wrapped->toArray());
		
		$this->assertEquals($user->addresses[0]->type, $values['addresses'][0]['type'] );
		
		$this->assertEquals('tony.pernicano', $wrapped->getUsername());
		$this->assertEquals('tony.pernicano', $wrapped->username);
		$this->assertTrue(isset($wrapped->username));
		
		$this->assertEquals('tcrollerderby.com', $wrapped->getDomain());
		$this->assertEquals('tcrollerderby.com', $wrapped->domain);
		$this->assertTrue(isset($wrapped->domain));
		
		//print_r(get_class($wrapped->getObject()));
	}
	
	public function testHydrator()
	{
		$userArray = $this->userArrayProvider();
		$user = new Google_Service_Directory_User();
		$wrappedUser = new TCRD\Wrapper\UserWrapper($user);
		
		$wrappedUser->hydrate($userArray);
		
		$this->assertEquals('tony.pernicano@tcrollerderby.com', $user->primaryEmail);
		$this->assertEquals('Tony', $user->name->givenName);
		
		$this->assertInstanceOf('Google_Service_Directory_UserName', $user->name);
		
		foreach ($userArray['addresses'] as $key => $addressArray) {
			$this->assertArrayHasKey($key, $wrappedUser->addresses);
			$this->assertInstanceOf('Google_Service_Directory_UserAddress', $user->addresses[$key]);
			$this->assertEquals($addressArray['locality'], $user->addresses[$key]->locality);
		}
	}
	
	public function userArrayProvider()
	{
		return array(
				'primaryEmail' => 'tony.pernicano@tcrollerderby.com',
				'name' => array(
						'givenName' => 'Tony',
						'familyName' => 'Pernicano',
						'fullName' => 'Tony Pernicano'
				),
				'addresses' => array(
						array(
								'type' => 'Home',
								'customType' => '',
								'streetAddress' => '1234 Bad Rd',
								'locality' => 'Detroit',
								'region' => 'MI',
								'postalCode' => '49675',
						)
				)	
		);
	}
}
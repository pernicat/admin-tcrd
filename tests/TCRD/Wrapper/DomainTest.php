<?php
namespace TCRD\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Google_Service_Directory as Directory;
use Google_Service_Directory_User as User;
use TCRD\Domain;

class DomainTest extends TestCase
{
	/**
	 * @dataProvider mockDirectoryProvider
	 * @param  $directory
	 * @param string $name
	 */
	public function testListUsers($directory, $name)
	{
		$domain = new Domain($directory, $name);
		
		$users = $domain->listUsers();
		
		$this->assertInternalType('array', $users);
		
		$this->assertInstanceOf('User', $users[0]);
		
		$this->assertEquals('first.last1@domain.com',  $users[0]->primaryEmail);
		
	}
	
	/**
	 * 
	 * @return multitype:Directory
	 */
	public function mockDirectoryProvider()
	{
		$users = array();
		
		for ($i = 1; $i <= 20; $i++) {
			$user = new User();
			$user->primaryEmail = "first.last$i@domain.com";
			$users[] = $user;
		}
		
		$directoryStud = $this->getMockBuilder('Google_Service_Directory')
				->disableOriginalConstructor()
			 	->getMock();
		
		$usersStud = $this->getMockBuilder('Google_Service_Directory_Users')
				->disableOriginalConstructor()
			 	->getMock();
		
		$usersStud->method('listUsers')
				->willReturn($users);
		
		$directoryStud->__set('users', $usersStud);
		
		return array(array($directoryStud, 'domain.com'));
	}
}
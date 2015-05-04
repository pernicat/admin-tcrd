<?php
namespace TCRD\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Google_Service_Directory as Directory;
use Google_Service_Directory_User as User;
use Google_Collection as Collection;
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
		
		//$this->assertInstanceOf('Google_Collection', $users);
		
		//$this->assertInstanceOf('Google_Service_Directory_Users', $users[0]);
		
		$this->assertEquals('first.last0@domain.com',  $users[0]->primaryEmail);
		
	}
	
	/**
	 * 
	 * @return multitype:Directory
	 */
	public function mockDirectoryProvider()
	{
		$users = array();
		
		for ($i = 0; $i < 20; $i++) {
			$user = new User();
			$user->primaryEmail = "first.last$i@domain.com";
			$users[] = $user;
		}
		
		$collection = new Collection(array('items' => $users));
		
		$directoryStud = $this->getMockBuilder('Google_Service_Directory')
				->disableOriginalConstructor()
			 	->getMock();
		
		$usersStud = $this->getMockBuilder('Google_Service_Directory_Users')
				->setMethods(array('listUsers'))
				->disableOriginalConstructor()
			 	->getMock();
		
		$usersStud->method('listUsers')
				->willReturn($collection);
		
		$directoryStud->users =$usersStud;
		
		return array(array($directoryStud, 'domain.com'));
	}
}
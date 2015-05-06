<?php
namespace TCRD\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use Google_Service_Directory as Directory;
use Google_Service_Directory_User as User;
use Google_Service_Directory_Users as Users;
use Google_Service_Directory_Group as Group;
use Google_Collection as Collection;
use TCRD\Domain;

class DomainTest extends TestCase
{
	/**
	 * @dataProvider mockDirectoryProvider
	 * @param $directory
	 * @param string $name
	 */
	public function testListUsers($directory, $name)
	{
		$domain = new Domain($directory, $name);
		
		$users = $domain->listUsers();
		
		$this->assertInstanceOf('TCRD\Wrapper\CollectionWrapper', $users);
		$this->assertEquals('first.last0@domain.com',  $users[0]->primaryEmail);
		
	}
	
	/**
	 * @dataProvider mockDirectoryProvider
	 * @param  $directory
	 * @param string $name
	 */
	public function testGetUsers($directory, $name)
	{
		$domain = new Domain($directory, $name);
	
		$users = $domain->getUsers();
	
		$this->assertInstanceOf('TCRD\Wrapper\CollectionWrapper', $users);
		$this->assertEquals('first.last0@domain.com',  $users[0]->primaryEmail);
		
		$usersArray = $users->toArray();
		
		foreach ($usersArray as $user) {
			$this->assertInstanceOf('TCRD\Wrapper\UserWrapper', $user);
		}
		
	}
	
	/**
	 * @dataProvider mockDirectoryProvider
	 * @param  $directory
	 * @param string $name
	 */
	public function testListGroups($directory, $name)
	{
		$domain = new Domain($directory, $name);
	
		$users = $domain->listGroups();
	
		$this->assertInstanceOf('TCRD\Wrapper\CollectionWrapper', $users);
		$this->assertEquals('group0',  $users[0]->id);
	
	}
	
	/**
	 * @dataProvider mockDirectoryProvider
	 * @param  $directory
	 * @param string $name
	 */
	public function testGetGroups($directory, $name)
	{
		$domain = new Domain($directory, $name);
	
		$users = $domain->getGroups();
	
		$this->assertInstanceOf('TCRD\Wrapper\CollectionWrapper', $users);
		$this->assertEquals('group0',  $users[0]->id);
	}
	
	/**
	 * 
	 * @return multitype:multitype:Directory|string
	 */
	public function mockDirectoryProvider()
	{
		$directoryStud = $this->getMockBuilder('Google_Service_Directory')
				->disableOriginalConstructor()
			 	->getMock();
		
		
		
		$usersStud = $this->getMockBuilder('Google_Service_Directory_Users_Resource')
				->setMethods(array('listUsers'))
				->disableOriginalConstructor()
			 	->getMock();
		
		$users = array();
		for ($i = 0; $i < 20; $i++) {
			$user = new User();
			$user->primaryEmail = "first.last$i@domain.com";
			$users[] = $user;
		}
		$usersCollection = new Users(array('users' => $users));
		
		$usersStud->method('listUsers')
				->willReturn($usersCollection);
		
		$directoryStud->users = $usersStud;
		
		
		
		$groupsStud = $this->getMockBuilder('Google_Service_Directory_Groups')
				->setMethods(array('listGroups'))
				->disableOriginalConstructor()
			 	->getMock();
		
		$groups = array();
		for ($i = 0; $i < 20; $i++) {
			$group = new Group();
			$group->id = "group$i";
			$groups[] = $group;
		}
		$groupsCollection = new Collection(array('items' => $groups));
		
		$groupsStud->method('listGroups')
				->willReturn($groupsCollection);
		
		$directoryStud->groups = $groupsStud;
		
		
		
		return array(array($directoryStud, 'domain.com'));
	}
}
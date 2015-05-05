<?php
namespace TCRD\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use TCRD\Domain;
use TCRD\Domains;
use Google_Service_Directory_Users as Users;
use Google_Service_Directory_User as User;


class DomainsTest extends TestCase
{
	/**
	 * @dataProvider mockDomainProvider
	 * @param array $domainsArray
	 */
	public function testAddDomain(Domains $domains, Array $domainsArray)
	{
		$domains = new Domains();
		
		foreach ($domainsArray as $key => $value) {
			$domains->addDomain($value);
			$this->assertSame($value, $domains->domains[$key]);
		}
	}
	
	/**
	 * 
	 * @return multitype:multitype:Domain
	 */
	public function mockDomainProvider() 
	{
		$allUsers = array();
		
		$domains = new Domains();
		
		$domainArray = array();
		for ($i = 0; $i < 4; $i++) {
			$directory = $this->getMockBuilder('Google_Service_Directory')
					->disableOriginalConstructor()
				 	->getMock();
			
			$name = "domain$i.com";
			
			$usersArray = array();
			for ($j = 0; $j < 10; $j++) {
				$user = new User();
				$user->primaryEmail = "test.user$j@$name";
				$usersArray[] = $user;
				$allUsers = $user;
			}
			
			$directory->users = new Users(array('users' => $usersArray));
			
			
			$domain = new Domain($directory, $name);
			$domainArray[] = $domain;
			$domains->addDomain($domain);
		}
		
		return array(array($domains, $domainArray, $allUsers));
	}
	
}
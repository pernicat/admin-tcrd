<?php
namespace TCRD\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use TCRD\Domain;
use TCRD\Domains;


class DomainsTest extends TestCase
{
	/**
	 * @dataProvider mockDomainProvider
	 * @param array $domainsArray
	 */
	public function testAddDomain(Array $domainsArray)
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
		$domainArray = array();
		for ($i = 0; $i < 4; $i++) {
			$domain = $this->getMockBuilder('TCRD\Domain')
					->disableOriginalConstructor()
				 	->getMock();
			
			$domainArray[] = $domain;
		}
		
		return array(array($domainArray));
	}
}
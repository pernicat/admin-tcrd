<?php
namespace TCRD\Tests;

use PHPUnit_Framework_TestCase as TestCase;
use TCRD\Domain;
use TCRD\Domains;


class DomainsTest extends TestCase
{
	
	public function testAddDomain()
	{
		$domains = new Domains();
		
		$domain1 = $this->mockDomainProvider();
		$domain2 = $this->mockDomainProvider();
		
		
		$domains->addDomain($domain1);
		$domains->addDomain($domain2);
		
		$this->assertSame($domain1, $domains->domains[0]);
		$this->assertSame($domain2, $domains->domains[1]);
	}
	
	/**
	 * @return Domain
	 */
	public function mockDomainProvider() 
	{
		$domain = $this->getMockBuilder('TCRD\Domain')
				->disableOriginalConstructor()
			 	->getMock();
		
		return $domain;
	}
}
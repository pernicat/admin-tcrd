<?php

class ClientWrapperTest extends PHPUnit_Framework_TestCase
{
	/**
	 * 
	 * @var Google_Client
	 */
	protected $mockClient;
	
	/**
	 * 
	 * @var TCRD\File\FileAccess
	 */
	protected $mockAccessTokenFile;
	
	/**
	 * 
	 * @var TCRD\Wrapper\ClientWrapper
	 */
	protected $clientWrapper;
	
	/**
	 * (non-PHPdoc)
	 * @see PHPUnit_Framework_TestCase::setUp()
	 */
	public function setUp() 
	{	
		$token = json_encode($this->accessTokenProvider());
		
		$this->mockClient = $this->getMockBuilder('Google_Client')->getMock();
		
		$this->mockClient->method('getAccessToken')
		                 ->willReturn($token);
		
		
		$this->mockAccessTokenFile = 
			$this->getMockBuilder('TCRD\\File\\FileAccess')
			 	 ->disableOriginalConstructor()
			 	 ->getMock();
		
		$this->mockAccessTokenFile->method('load')
								  ->willReturn($token);
		
		$this->mockAccessTokenFile->method('save')
		     ->willReturn($this->mockAccessTokenFile);
		
		
		$this->clientWrapper = 
				new TCRD\Wrapper\ClientWrapper($this->mockClient);
	}
	
	/**
	 * 
	 * 
	 */
	public function testGetTokenException()
	{
		$this->clientWrapper->getAccessToken();
	}
	
	/**
     * @depends testGetTokenException
	 */
	public function testSetFile()
	{
		$this->clientWrapper->setAccessTokenFile($this->mockAccessTokenFile);
		
		$token = $this->clientWrapper->getAccessToken();
		
		//print_r($token);
		
		$this->assertInternalType('array', $token);
		
		$this->assertEquals('Bearer', $token['token_type']);
		$this->assertEquals(3600, $token['expires_in']);
	}
	
	/**
     * 
	 */
	public function accessTokenProvider()
	{
		return array(
				"access_token" => "blablablabla",
				"token_type" => "Bearer",
				"expires_in" => 3600,
				"refresh_token" => "boblablaw",
				"created" => 1421883955
		);
	}
	
}
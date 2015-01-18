<?php
namespace TCRD;

/**
 * 
 * @author Tony Pernicano
 *
 */
class ClientContainer
{
	/**
	 * 
	 * @var \Google_Client
	 */
	protected $client;
	
	/**
	 * 
	 * @var string
	 */
	protected $accessFile;
	
	/**
	 * 
	 * @var string
	 */
	protected $refreshFile;
	
	/**
	 * 
	 * @param \Google_Client $client
	 * @param string $accessFile
	 * @param string $refreshFile
	 */
	public function __construct(\Google_Client $client, $accessFile, $refreshFile)
	{
		$this->client = $client;
		$this->accessFile = $accessFile;
		$this->refreshFile = $refreshFile;
	}
	
	/**
	 * 
	 * @return Google_Client
	 */
	public function getClient() 
	{
		$this->getAccessToken();
		return $this->client;
	}
	
	/**
	 * @todo clean this up
	 * @return string
	 */
	public function getAccessToken()
	{
		$accessToken = $this->client->getAccessToken();
		if (!$accessToken) {
			$accessToken = file_get_contents($this->accessFile);
			if ($accessToken) {
				$this->client->setAccessToken($accessToken);
			}
		}

		if (!$accessToken) {
			$accessToken = $this->authenticate();
		}

		if ($this->client->isAccessTokenExpired()) {
			$refreshToken = file_get_contents($this->refreshFile);
			$this->client->refreshToken($refreshToken);

			$accessToken = $this->client->getAccessToken();
			
			//print_r($accessToken);
			
			file_put_contents($this->accessFile, $accessToken);
			$this->client->setAccessToken($accessToken);
		}
		return $accessToken;
	}
	
	/**
	 * 
	 * @return string
	 */
	protected function authenticate()
	{
		$authUrl = $this->client->createAuthUrl();
			
		//Request authorization
		print "Please visit:\n$authUrl\n\n";
		print "Please enter the auth code:\n";
		$authCode = trim(fgets(STDIN));
						
		// Exchange authorization code for access token
		$accessToken = $this->client->authenticate($authCode);
		print $accessToken;
		file_put_contents($this->accessFile, $accessToken);
		$this->client->setAccessToken($accessToken);
		
		$refreshToken = $this->client->getRefreshToken();
		if ($refreshToken) {
			print $refreshToken;
			file_put_contents($this->refreshFile, $refreshToken);
		}
		
		return $accessToken;
	}
	
}
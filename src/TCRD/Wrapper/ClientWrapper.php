<?php
namespace TCRD\Wrapper;

class ClientWrapper
{
	/**
	 * 
	 * @var \Google_Client
	 */
	protected $client;
	
	/**
	 * 
	 * @var \TCRD\File\FileAccess
	 */
	protected $accessTokenFile;
	
	/**
	 * 
	 * @var string
	 */
	public $name;
	
	/**
	 * 
	 * @param \Google_Client $client
	 */
	public function __construct(\Google_Client $client)
	{
		$this->client = $client;
	}
	
	/**
	 * 
	 * @param array $config
	 * @return \TCRD\Wrapper\ClientWrapper
	 */
	public function configure($config) {
		
		$this->client->setClientId($config['client_id']);
		$this->client->setClientSecret($config['client_secret']);
		$this->client->setRedirectUri($config['redirect_uri']);
		$this->client->setScopes($config['scopes']);
		$this->client->setAccessType($config['access_type']);
		$this->client->setApprovalPrompt($config['approval_prompt']);
		
		return $this;
	}
	
	/**
	 * 
	 * @param \TCRD\File\FileAccess $location
	 */
	public function setAccessTokenFile(\TCRD\File\FileAccess $file) 
	{
		$this->accessTokenFile = $file;
	}
	
	/**
	 * 
	 * @return string
	 */
	public function createAuthUrl()
	{
		return $this->client->createAuthUrl();
	}
	
	/**
	 * 
	 * @param string $code
	 * @return \TCRD\Wrapper\ClientWrapper
	 */
	public function authenticate($code)
	{
		$accessToken = $this->client->authenticate($code);
		$this->saveAccessToken($accessToken);
		return $this;
	}
	
	/**
	 * 
	 * @return Google_Client
	 */
	public function getClient() {
		// Makes sure that the accessToken is valid
		$this->updateAccessToken();
		return $this->client;
	}
	
	/**
	 * 
	 * @return \TCRD\Wrapper\ClientWrapper
	 */
	protected function updateAccessToken()
	{	
		if (!$this->accessTokenFile) {
			throw new \Exception("accessTokenFile has not been set");
		}
		
		if (!$this->client->getAccessToken()) {
			$token = $this->accessTokenFile->load();
			
			$this->client->setAccessToken($token);
		}
		
		if ($this->client->isAccessTokenExpired()) {
			$token = json_decode($this->client->getAccessToken());
			
			$this->client->refreshToken($token->refresh_token);
			
			$this->accessTokenFile->save($this->client->getAccessToken());
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @throws \Exception
	 * @return array
	 */
	public function getAccessToken()
	{
		$this->updateAccessToken();
		
		$jsonToken = $this->client->getAccessToken();
		
		$token = json_decode($jsonToken, true);
		
		if (null == $token) {
			throw new \Exception("could not decode json from '$jsonToken'");
		}
		
		return $token;
	}
}
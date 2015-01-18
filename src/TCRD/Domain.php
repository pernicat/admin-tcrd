<?php
namespace TCRD;

class Domain
{
	/**
	 * 
	 * @var ClientContainer
	 */
	protected $clientContainer;
	
	/**
	 * 
	 * @var array
	 */
	protected $specs;
	
	/**
	 * 
	 * @var \Google_Service_Directory
	 */
	protected $directory;
	
	/**
	 * 
	 * @param ClientContainer $clientContainer
	 * @param array|string $specs
	 */
	public function __construct(ClientContainer $clientContainer, $specs) 
	{
		$this->clientContainer = $clientContainer;
		
		if (is_string($specs)) {
			$specs = array('name' => $specs);
		}
		
		$this->setSpecs($specs);
	}
	
	protected function setSpecs($specs) {
		if (!isset($specs['name'])) {
			throw new \Exception("specs must specify a name");
		}
		
		$this->specs = $specs;
	}
	
	/**
	 * @return \Google_Client
	 */
	public function getClient()
	{
		return $this->clientContainer->getClient();
	}
	
	/**
	 * 
	 * @return \Google_Service_Directory
	 */
	public function getDirectory()
	{
		if (null == $this->directory) {
			$client = $this->getClient();
			$this->directory = new \Google_Service_Directory($client);
		}
		return $this->directory;
	}
	
	/**
	 * 
	 * @return string:
	 */
	public function getName()
	{
		// TODO error checking
		return $this->specs['name'];
	}
	
	/**
	 * 
	 * @param array $parms
	 * @return Google_Service_Directory_Users
	 */
	public function listUsers($parms = array())
	{
		if (!is_array($parms)) {
			throw new \Exception("\$params must be an array.");
		}
		$parms['domain'] = $this->getName();
		$directory = $this->getDirectory();
		return $directory->users->listUsers($parms);
		
	}
}
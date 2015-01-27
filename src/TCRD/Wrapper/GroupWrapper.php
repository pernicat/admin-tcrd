<?php
namespace TCRD\Wrapper;

class GroupWrapper extends ModelWrapper
{
	/**
	 * 
	 * @var \Google_Service_Directory_Group
	 */
	protected $object;
	
	/**
	 * 
	 * @var \Google_Service_Directory
	 */
	protected $directory;
	
	/**
	 * 
	 * @var \TCRD\Wrapper\ColletionWrapper
	 */
	protected $members;
	
	/**
	 * 
	 * @var string
	 */
	protected $memberClass = '\\TCRD\\Wrapper\\ModelWrapper';
	
	/**
	 * 
	 * @param \Google_Service_Directory_Group $group
	 * @param array $args
	 */
	public function __construct(\Google_Service_Directory_Group $group, $args) 
	{
		parent::__construct($group, $args);
	}
	
	/**
	 * 
	 * @param \Google_Service_Directory $directory
	 * @return \TCRD\Wrapper\GroupWrapper
	 */
	public function setDirectory(\Google_Service_Directory $directory) {
		$this->directory = $directory;
		return $this;
	}
	
	/**
	 * 
	 * @throws \Exception
	 * @return Google_Service_Directory
	 */
	public function getDirectory()
	{
		if (!$this->directory) {
			throw new \Exception("directory not set");
		}
		return $this->directory;
	}
	
	/**
	 * 
	 * @param array $parms
	 * @return \TCRD\Wrapper\ColletionWrapper
	 */
	public function listMembers($parms = array())
	{
		$directory = $this->getDirectory();
		
		$members = $directory->members->listMembers($this->getEmail(), $parms);
		
		$wrappedMembers = new ColletionWrapper($members, $config);
		
		// TODO set class
		// TODO cache results
		
		return $wrappedMembers;
	}
	
	/**
	 * 
	 * @return \TCRD\Wrapper\ColletionWrapper
	 */
	public function getMembers()
	{
		if (!$this->members) {
			$this->members = $this->listMembers();
		}
		
		return $this->members;
	}
}
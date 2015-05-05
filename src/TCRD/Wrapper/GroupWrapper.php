<?php
namespace TCRD\Wrapper;

use Google_Service_Directory_Group as Group;
use Google_Service_Directory as Directory;
use Exception;

class GroupWrapper extends ModelWrapper
{
	/**
	 * 
	 * @var Group
	 */
	protected $object;
	
	/**
	 * 
	 * @var Directory
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
	protected $memberClass = 'TCRD\Wrapper\ModelWrapper';
	
	/**
	 * 
	 * @param Group $group
	 * @param array $args
	 */
	public function __construct(Group $group, $config = array()) 
	{
		parent::__construct($group, $config);
	}
	
	/**
	 * 
	 * @param Directory $directory
	 * @return \TCRD\Wrapper\GroupWrapper
	 */
	public function setDirectory(Directory $directory) {
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
			throw new Exception("directory not set");
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
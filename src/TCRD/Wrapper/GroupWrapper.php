<?php
namespace TCRD\Wrapper;

class GroupWrapper
{
	/**
	 * 
	 * @var \Google_Service_Directory_Group
	 */
	protected $group;
	
	/**
	 * 
	 * @var \Google_Service_Directory
	 */
	protected $directory;
	
	/**
	 * 
	 * @param \Google_Service_Directory $directory
	 * @param \Google_Service_Directory_Group $group
	 */
	public function __construct(
			\Google_Service_Directory $directory, 
			\Google_Service_Directory_Group $group) 
	{
		$this->directory = $directory;
		$this->group = $group;
		
	}
	
	public function listMembers($parms)
	{
		
	}
}
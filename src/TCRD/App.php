<?php
namespace TCRD;

use TCRD\Worksheet\Roster;


class App
{
	/**
	 * 
	 * @var array
	 */
	protected $domains = array();
	
	/**
	 * 
	 * @var Roster
	 */
	protected $roster;
	
	public function __construct(Roster $roster, $domains)
	{
		$this->roster = $roster;
		
		if (!is_array($domains)) {
			$domains = array($domains);
		}
		
		$this->domains = $domains;
	}
	
}
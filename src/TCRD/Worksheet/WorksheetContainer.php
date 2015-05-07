<?php
namespace TCRD\Worksheet;

use Exception;
use Google\Spreadsheet\Worksheet;
use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\ListEntry;
use TCRD\Worksheet\Entry;

/**
 * 
 * @author Tony Pernicano
 * @todo this may need to be changed for new Google API
 *
 */
class WorksheetContainer
{
	/**
	 * 
	 * @var Worksheet
	 */
	protected $worksheet;
	
	/**
	 *
	 * @var array
	 */
	protected $index = array();
	
	/**
	 * 
	 * @var array
	 */
	protected $entries;
	
	/**
	 * 
	 * @param Worksheet $sheet
	 */
	public function __construct(Worksheet $worksheet)
	{
		$this->worksheet = $worksheet;
	}
	
	/**
	 * 
	 * @return Worksheet
	 */
	public function getWorksheet()
	{
		return $this->worksheet;
	}
	
	/**
	 * 
	 * @return ListFeed
	 */
	public function getListFeed()
	{
		return $this->worksheet->getListFeed();
	}
	
	/**
	 * 
	 * @param array $where
	 * @return multitype:ListEntry
	 */
	public function find($where)
	{
		if (!is_array($where)) {
			throw new Exception("\$where must be an assosiative array\n");
		}
		
		$results = array();
		
		/* @var $entry Entry */
		foreach ($this->getEntries() as $entry) {
			foreach ($where as $k => $v) {
				if (!$entry__isset($k)) {
					throw new Exception("index $k not found\n");
				}
				
				if ($entry__get($k) != $v) {
					// lazy
					continue 2;
				}
			}
			
			$results[] = $entry;
		}
		
		return $results;
	}
	
	/**
	 * 
	 * @param string $field
	 * @throws Exception
	 * @return multitype:Entry
	 */
	public function getUniquIndex($field) 
	{
		if (!array_key_exists($field, $this->index)) {
	
			$this->index[$field] = array();
			$listFeed = $this->getListFeed();
				
			/* @var $entry Entry */
			foreach ($this->getEntries() as $entry) {
	
				if (!$entry->__isset($field)) {
					throw new Exception("field $field does not exist\n");
				}
					
				$key = trim(strtolower($entry->__get($field)));
	
				if (isset($this->index[$field][$key])) {
					throw new Exception("field $field must be unique $key exist more then once\n");
				}
	
				$this->index[$field][$key] = $entry;
			}
		}
		return $this->index[$field];
	}
	
	/**
	 * 
	 * @return multitype:Entry
	 */
	public function getEntries()
	{
		if (!$this->entries) {
			$this->entries = array();
			
			$listFeed = $this->getListFeed();
			
			foreach ($listFeed->getEntries() as $listEntry) {
				$this->entries[] = new Entry($listEntry);
			}
		}
		return $this->entries;
	}
}
<?php
namespace TCRD\Worksheet;

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
	 * @var \Google\Spreadsheet\Worksheet
	 */
	protected $worksheet;
	
	/**
	 *
	 * @var array
	 */
	protected $index = array();
	
	/**
	 * 
	 * @param \Google\Spreadsheet\Worksheet $sheet
	 */
	public function __construct(\Google\Spreadsheet\Worksheet $worksheet)
	{
		$this->worksheet = $worksheet;
	}
	
	/**
	 * 
	 * @return \Google\Spreadsheet\Worksheet
	 */
	public function getWorksheet()
	{
		return $this->worksheet;
	}
	
	/**
	 * 
	 * @return \Google\Spreadsheet\List\Feed
	 */
	public function getListFeed()
	{
		return $this->worksheet->getListFeed();
	}
	
	/**
	 * 
	 * @param array $where
	 * @return multitype:\TCRD\Google\Spreadsheet\ListEntry
	 */
	public function find($where)
	{
		if (!is_array($where)) {
			throw new \Exception("\$where must be an assosiative array\n");
		}
		
		$this->index[$name] = array();
		$listFeed = $this->getListFeed();

		$results = array();
		
		/* @var $entry Google\Spreadsheet\ListEntry */
		foreach ($listFeed->getEntries() as $entry) {
			$values = $entry->getValues();
			
			foreach ($where as $k => $v) {
				if (!isset($values[$k])) {
					throw new \Exception("index $k not found\n");
				}
				
				if ($values[$k] != $v) {
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
	 * @throws \Exception
	 * @return array:
	 */
	public function getIndex($field) 
	{
	
		if (!isset($this->index[$field])) {
	
			$this->index[$field] = array();
			$listFeed = $this->getListFeed();
				
			/* @var $entry Google\Spreadsheet\ListEntry */
			foreach ($listFeed->getEntries() as $entry) {
	
				$values = $entry->getValues();
	
				if (!isset($values[$field])) {
					throw new \Exception("field $field does not exist\n");
				}
					
				$key = trim(strtolower($values[$field]));
	
				if (isset($this->index[$field][$key])) {
					throw new \Exception("field $field must be unique $key exist more then once\n");
				}
	
				$this->index[$field][$key] = $entry;
			}
		}
		return $this->index[$field];
	}
}
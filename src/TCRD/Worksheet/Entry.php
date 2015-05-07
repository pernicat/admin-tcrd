<?php
namespace TCRD\Worksheet;

use Google\Spreadsheet\ListEntry;
use Exception;
/**
 * 
 * @author Tony Pernicano
 *
 */
class Entry
{
	/**
	 * 
	 * @var ListEntry
	 */
	protected $listEntry;
	
	/**
	 * 
	 * @var multitype:string
	 */
	protected $values;
	
	/**
	 * 
	 * @var boolean
	 */
	protected $clean = true;
	
	/**
	 * 
	 * @param ListEntry $listEntry
	 */
	public function __construct(ListEntry $listEntry)
	{
		$this->listEntry = $listEntry;
	}
	
	/**
	 * 
	 * @param scalar $name
	 * @return boolean
	 */
	public function __isset($name)
	{
		$values = $this->toArray();
		return array_key_exists($name, $values);
	}
	
	/**
	 * 
	 * @param scalar $name
	 */
	public function __get($name)
	{
		$values = $this->toArray();
		
		if (array_key_exists($name, $values)) {
			return $values[$name];
		}
		
		throw new Exception("Undefined property via __get(): '$name'");
	}
	
	/**
	 * 
	 * @param scalar $name
	 * @param mixed $value
	 */
	public function __set($name, $value)
	{
		// make sure $this->values is initialized
		$this->toArray();
		$this->values[$name] = $value;
		$this->clean = false;
	}
	
	/**
	 * 
	 * @return \TCRD\Worksheet\Entry
	 */
	public function save()
	{
		if (!$this->clean) {
			$this->listEntry->update($this->toArray());
			$this->clean = true;
		}
		
		return $this;
	}
	
	/**
	 * 
	 * @return \TCRD\Worksheet\Entry
	 */
	public function delete()
	{
		$this->listEntry->delete();
		return $this;
	}
	
	/**
	 * 
	 * @return \TCRD\Worksheet\multitype:string
	 */
	public function toArray()
	{
		if (!$this->values) {
			$this->values = $this->listEntry->getValues();
		}
		return $this->values;
	}
}
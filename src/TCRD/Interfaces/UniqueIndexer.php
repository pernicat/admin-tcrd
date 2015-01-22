<?php
namespace TCRD\Interfaces;

interface UniqueIndexer
{
	
	/**
	 * 
	 * @param scalar $field
	 * @param scalar $value
	 * @return mixed
	 */
	public function getUnique($field, $value);
	
	/**
	 * 
	 * @param scalar $field
	 * @return array
	 */
	public function getUniqueIndex($field);
}
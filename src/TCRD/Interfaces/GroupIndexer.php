<?php
namespace TCRD\Interfaces;

interface GroupIndexer
{
	/**
	 *
	 * @param scalar $field
	 * @param scalar $value
	 * @return array
	 */
	public function getunique($field, $value);
	
	/**
	 *
	 * @param scalar $field
	 * @return array
	*/
	public function getUniqueIndex($field);
}
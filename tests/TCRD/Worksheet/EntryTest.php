<?php
namespace TCRD\Tests\Worksheet;

use PHPUnit_Framework_TestCase as TestCase;
use PHPUnit_Framework_MockObject_MockObject as MockObject;
use Google\Spreadsheet\ListEntry as ListEntry;
use TCRD\Worksheet\Entry;

class EntryTest extends TestCase
{
	/**
	 * @dataProvider listEntryMockProvider
	 * @param ListEntry $listEntry
	 * @param array $values
	 */
	public function testGet(ListEntry $listEntry, Array $values)
	{
		$entry = new Entry($listEntry);
		
		foreach ($values as $key => $value) {
			$this->assertEquals($value, $entry->__get($key));
		}
		
	}
	
	/**
	 * @dataProvider listEntryMockProvider
	 * @param ListEntry $listEntry
	 * @param array $values
	 */
	public function testIsset(ListEntry $listEntry, Array $values)
	{
		$entry = new Entry($listEntry);
	
		foreach ($values as $key => $value) {
			$this->assertTrue($entry->__isset($key));
		}
	
	}
	
	/**
	 * @dataProvider listEntryMockProvider
	 * @param ListEntry $listEntry
	 * @param array $values
	 */
	public function testSet(ListEntry $listEntry, Array $values)
	{
		$entry = new Entry($listEntry);
	
		$name = 'Kate';
		
		$entry->givenName = $name;
		
		$this->assertEquals($name, $entry->givenName);
	}
	
	/**
	 * @dataProvider listEntryMockProvider
	 * @param ListEntry $listEntry
	 * @param array $values
	 */
	public function testSave(ListEntry $listEntry, Array $values)
	{
		$entry = new Entry($listEntry);
		
		$entry->save();
		
		$this->assertEquals($values, $entry->toArray());
	}
	
	/**
	 * @dataProvider listEntryMockProvider
	 * @param ListEntry $listEntry
	 * @param array $values
	 */
	public function testToArray(ListEntry $listEntry, Array $values)
	{
		$entry = new Entry($listEntry);
		
		$this->assertEquals($values, $entry->toArray());
	}
	
	/**
	 * 
	 * @return multitype:multitype:ListEntry
	 */
	public function listEntryMockProvider()
	{
		$values = array(
			'givenName' => 'Tony',
			'familyName' => 'Pernicano',
			'primaryEmail' => 'pernicat@gmail.com'		
		);
		
		/* @var $listEntryStud MockObject */
		$listEntryMock = $this->getMockBuilder('Google\Spreadsheet\ListEntry')
				->disableOriginalConstructor()
				->getMock();
		
		$listEntryMock->method('getValues')
				->willReturn($values);
		
		//echo get_class($listEntryMock);
		
		return array(array($listEntryMock, $values));
	}
}
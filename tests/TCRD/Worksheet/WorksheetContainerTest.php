<?php
namespace TCRD\Tests\Worksheet;

use PHPUnit_Framework_TestCase as TestCase;
use Google\Spreadsheet\Worksheet;
use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\ListEntry;
use TCRD\Worksheet\WorksheetContainer as Container;
use TCRD\Worksheet\Entry;

/**
 * 
 * @author Tony Pernicano
 *
 */
class WorksheetContaainerTest extends TestCase
{
	/**
	 * @dataProvider mockWorksheetProvider
	 * @param Worksheet $worksheet
	 * @param array $listEntries
	 */
	public function testFind(
			Worksheet $worksheet,
			Array $listEntries)
	{
		$container = new Container($worksheet);
		
		$result = $container->find(array('thing' => 'even'));
		
		foreach ($result as $entry) {
		
			$this->assertEquals(0, $entry->i % 2);
		}
	}
	
	/**
	 * @dataProvider mockWorksheetProvider
	 * @param Worksheet $worksheet
	 * @param array $listEntries
	 */
	public function testGetUniqueIndex(
			Worksheet $worksheet, 
			Array $listEntries)
	{
		$container = new Container($worksheet);
		
		$index = $container->getUniquIndex('name');
			
		$this->assertArrayHaskey('user1', $index);

		
	}
	
	/**
	 * @dataProvider mockWorksheetProvider
	 * @param Worksheet $worksheet
	 * @param array $listEntries
	 */
	public function testGetEntries(
			Worksheet $worksheet, 
			Array $listEntries)
	{
		$container = new Container($worksheet);
		
		$entries = $container->getEntries();
		
		foreach ($entries as $key => $entry) {
			
			$this->assertInstanceOf('TCRD\Worksheet\Entry', $entry);
			
			/* @var $listEntry ListEntry */
			$listEntry = $listEntries[$key];
			$this->assertEquals($listEntry->getValues(), $entry->toArray());
		}
	}
	
	/**
	 * 
	 * @return multitype:multitype:Worksheet multitype:ListEntry
	 */
	public function mockWorksheetProvider()
	{
		/* @var $listEntryStud MockObject */
		$worksheetMock = $this->getMockBuilder('Google\Spreadsheet\Worksheet')
				->disableOriginalConstructor()
				->getMock();
		
		/* @var $listEntryStud MockObject */
		$listFeedMock = $this->getMockBuilder('Google\Spreadsheet\ListFeed')
			->disableOriginalConstructor()
			->getMock();
		
		
		$listEntiesMocks = array();
		
		for ($i = 0; $i < 20; $i++) {
			/* @var $listEntryStud MockObject */
			$listEntryMock = $this->getMockBuilder('Google\Spreadsheet\ListEntry')
					->disableOriginalConstructor()
					->getMock();
			
			$values = array(
				'name' => "user$i",
				'primaryEmail' => "user$i@domain.com",
				'thing' => $i % 2 == 0 ? 'even' : 'odd',
				'i' => $i
			);
			
			$listEntryMock->method('getValues')
				->willReturn($values);
			
			$listEntiesMocks[] = $listEntryMock;
		}
		
		$listFeedMock->method('getEntries')
				->willReturn($listEntiesMocks);
		
		$worksheetMock->method('getListFeed')
				->willReturn($listFeedMock);
		
		return array(array($worksheetMock, $listEntiesMocks));
	}
}
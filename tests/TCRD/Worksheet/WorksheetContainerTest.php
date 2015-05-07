<?php
namespace TCRD\Tests\Worksheet;

use PHPUnit_Framework_TestCase as TestCase;
use Google\Spreadsheet\Worksheet;
use Google\Spreadsheet\ListFeed;
use Google\Spreadsheet\ListEntry;

class WorksheetContaainerTest extends TestCase
{
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
				'name' => "user$i"	
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
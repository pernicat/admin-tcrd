<?php

class ModelWrapperTest extends PHPUnit_Framework_TestCase
{
	public function testConstruct()
	{
		$model = $this->getMockBuilder('Google_Model')->getMock();
		$wrapper = new TCRD\Wrapper\ModelWrapper($model);
		
		$wrapper->offsetSet(0, 4);
		
		
		
	}
	
	public function testTings()
	{
		$this->assertEquals(0, 1-1);
	}
}
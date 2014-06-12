<?php

class toxidcurlTest extends \PHPUnit_Framework_TestCase
{
    public function testStub()
    {
        $this->markTestIncomplete("@TODO: Implement Test.");
    }

    public function test_getXmlObject()
    {
        $fakeCurl = $this->getMock('toxidCurl');
        $fakeCurl
            ->expects($this->any())
            ->method('_getXmlObject')
            ->will($this->returnValue('<?xml version="1.0"?><toxid><main>MAIN</main><nav>NAVIGATION</nav></toxid>'));


        $this->assertEquals('MAIN', $fakeCurl->test_getXmlObject('main'));
    }
}

<?php
namespace inroute\Tag;

class ControllerTagTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPath()
    {
        $tag = $this->getMock(
            'phpDocumentor\Reflection\DocBlock\Tag',
            array(),
            array(),
            '',
            false
        );

        $controllerTag = new ControllerTag($tag);

        $this->assertEquals('', $controllerTag->getPath());
    }
}

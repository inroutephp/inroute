<?php
namespace iio\inroute\Tag;

class RouteTagTest extends \PHPUnit_Framework_TestCase
{
    private function getTagWithDesc($desc)
    {
        $tag = $this->getMock(
            'phpDocumentor\Reflection\DocBlock\Tag',
            array('getDescription'),
            array(),
            '',
            false
        );

        $tag->expects($this->any())
            ->method('getDescription')
            ->will($this->returnValue($desc));

        return $tag;
    }

    /**
     * @expectedException \iio\inroute\Exception
     */
    public function testInvalidMethod()
    {
        $tag = new RouteTag($this->getTagWithDesc('GET,FOOBAR </www/www>'));
        $tag->getMethods();
    }

    /**
     * @expectedException \iio\inroute\Exception
     */
    public function testInvalidRouteDescription()
    {
        $tag = new RouteTag($this->getTagWithDesc(' GET '));
        $tag->getPath();
    }
}

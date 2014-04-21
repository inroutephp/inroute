<?php
namespace inroute\Router;

class SegmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $segment = new Segment(
            'foobar',
            new Regex('.+')
        );
        $this->assertEquals('foobar', $segment->getName());
        $this->assertEquals('(?<foobar>.+)', (string)$segment);
    }

    public function testSubstitute()
    {
        $segment = new Segment(
            '',
            new Regex('\d+')
        );
        $this->assertEquals('123', $segment->substitute('123'));

        // Substitute a non-matching value
        $this->setExpectedException('inroute\Exception\RuntimeException');
        $segment->substitute('foobar');
    }
}

<?php
namespace inroute\Runtime;

class PathSegmentTest extends \PHPUnit_Framework_TestCase
{
    public function testGetters()
    {
        $segment = new PathSegment(
            'foobar',
            new Regex('.+')
        );
        $this->assertEquals('foobar', $segment->getName());
        $this->assertEquals('(?<foobar>.+)', (string)$segment);
    }

    public function testSubstitute()
    {
        $segment = new PathSegment(
            '',
            new Regex('\d+')
        );
        $this->assertEquals('123', $segment->substitute('123'));

        // Substitute a non-matching value
        $this->setExpectedException('RuntimeException');
        $segment->substitute('foobar');
    }
}

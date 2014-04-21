<?php
namespace inroute\Router;

class RegexTest extends \PHPUnit_Framework_TestCase
{
    public function testDefault()
    {
        $regex = new Regex;
        $this->assertEquals('[^/]+', (string)$regex);
        $this->assertEquals('#^[^/]+$#', (string)$regex->getRegex());
    }

    public function testGetMatches()
    {
        $regex = new Regex('/path/(?<name>.+)');
        $this->assertEquals('', $regex->name);
        $this->assertTrue($regex->match('/path/foobar'));
        $this->assertEquals('foobar', $regex->name);
        $this->assertFalse($regex->match('/p/foobar'));
        $this->assertEquals('', $regex->name);
    }
}

<?php
namespace inroute\Router;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $that = $this;

        $route = new Route(
            array(),
            new Regex,
            array(),
            'cntrlClass',
            'cntrlMethod',
            function ($cntrl, $method, $route) use ($that) {
                $that->assertEquals('cntrlClass', $cntrl);
                $that->assertEquals('cntrlMethod', $method);
                $that->assertEquals('cntrlClass::cntrlMethod', $route->getName());
            }
        );

        $route();
    }

    public function testIsMethodMatch()
    {
        $route = new Route(
            array(),
            new Regex,
            array('GET'),
            '',
            '',
            function () {}
        );

        $this->assertEquals('', $route->getMethod());
        $this->assertTrue($route->isMethodMatch('GET'));
        $this->assertEquals('GET', $route->getMethod());
        $this->assertFalse($route->isMethodMatch('POST'));
        $this->assertEquals('', $route->getMethod());
    }

    public function testIsPathMatch()
    {
        $route = new Route(
            array(),
            new Regex('/path/(?<id>\d+)'),
            array(),
            '',
            '',
            function () {}
        );

        $this->assertEquals('', $route->getPath());
        $this->assertEquals('', $route->id);
        $this->assertTrue($route->isPathMatch('/path/123'));
        $this->assertEquals('/path/123', $route->getPath());
        $this->assertEquals('123', $route->id);
        $this->assertFalse($route->isPathMatch('/path/foobar'));
        $this->assertEquals('', $route->getPath());
        $this->assertEquals('', $route->id);
    }

    public function testGenerate()
    {
        $route = new Route(
            array(
                '',
                'path',
                new Segment(
                    'id',
                    new Regex('\d+')
                )
            ),
            new Regex,
            array(),
            '',
            '',
            function () {}
        );

        $this->assertEquals('/path/123', $route->generate(array('id' => '123')));

        // Generate with param id missing
        $this->setExpectedException('inroute\Exception\RuntimeException');
        $route->generate(array());
    }
}

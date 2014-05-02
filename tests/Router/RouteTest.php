<?php
namespace inroute\Router;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testInvoke()
    {
        $route = new Route(
            array(),
            new Regex,
            array(),
            'cntrlClass',
            'cntrlMethod',
            array(),
            array()
        );

        $that = $this;
        $route->invoke(
            function (array $args) use ($that) {
                $that->assertEquals('cntrlClass', $args['controller']);
                $that->assertEquals('cntrlMethod', $args['method']);
                $that->assertEquals('cntrlClass::cntrlMethod', $args['route']->getName());
            }
        );
    }

    public function testPreFilters()
    {
        $that = $this;
        $route = new Route(
            array(),
            new Regex,
            array(),
            'cntrlClass',
            'cntrlMethod',
            array(
                function (array &$args) use ($that) {
                    $that->assertFalse(isset($args['foo']));
                    $args['foo'] = 'bar';
                }
            ),
            array()
        );
        $route->invoke(
            function (array $args) use ($that) {
                $that->assertEquals('bar', $args['foo']);
            }
        );
    }

    public function testPostFilters()
    {
        $that = $this;
        $route = new Route(
            array(),
            new Regex,
            array(),
            'cntrlClass',
            'cntrlMethod',
            array(),
            array(
                function (&$return) use ($that) {
                    $that->assertEquals('returned-from-route', $return);
                    $return .= '-altered-in-filter';
                },
                function ($return) use ($that) {
                    $that->assertEquals('returned-from-route-altered-in-filter', $return);
                }
            )
        );
        $route->invoke(
            function (array $args) {
                return 'returned-from-route';
            }
        );
    }

    public function testIsMethodMatch()
    {
        $route = new Route(
            array(),
            new Regex,
            array('GET'),
            '',
            '',
            array(),
            array()
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
            array(),
            array()
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
            array(),
            array()
        );

        $this->assertEquals('/path/123', $route->generate(array('id' => '123')));

        // Generate with param id missing
        $this->setExpectedException('RuntimeException');
        $route->generate(array());
    }
}

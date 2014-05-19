<?php
namespace inroute\Router;

class RouteTest extends \PHPUnit_Framework_TestCase
{
    public function testExecuteRoute()
    {
        $env = \Mockery::mock('inroute\Router\Environment');
        $env->shouldReceive('set')->once();
        $env->shouldReceive('get')->once()->with('controller_name')->andReturn('ControllerClassName');
        $env->shouldReceive('get')->once()->with('controller_method')->andReturn('cntrlMethod');

        $route = new Route(
            [],
            \Mockery::mock('inroute\Router\Regex'),
            $env,
            ['PreFilterClassName'],
            ['PostFilterClassName']
        );

        // $instance mocks both controller and filters
        $instance = \Mockery::mock('stdClass');
        // controller expectation
        $instance->shouldReceive('cntrlMethod')->once()->with($env)->andReturn('controller-return');
        // pre-filter expectation
        $instance->shouldReceive('filter')->once()->with($env);
        // post-filter expectation
        $instance->shouldReceive('filter')->once()->with('controller-return')->andReturn('filter-return');

        $instantiator = \Mockery::mock('inroute\Router\Instantiator');
        $instantiator->shouldReceive('__invoke')->with('ControllerClassName')->andReturn($instance);
        $instantiator->shouldReceive('__invoke')->with('PreFilterClassName')->andReturn($instance);
        $instantiator->shouldReceive('__invoke')->with('PostFilterClassName')->andReturn($instance);

        $this->assertEquals(
            'filter-return',
            $route->execute($instantiator)
        );
    }

    public function testIsMethodMatch()
    {
        $env = \Mockery::mock('inroute\Router\Environment');
        $env->shouldReceive('get')->twice()->with('http_methods')->andReturn(['GET']);

        $route = new Route(
            [],
            \Mockery::mock('inroute\Router\Regex'),
            $env,
            [],
            []
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
            [],
            new Regex('/path/(?<id>\d+)'),
            \Mockery::mock('inroute\Router\Environment'),
            [],
            []
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
            \Mockery::mock('inroute\Router\Regex'),
            \Mockery::mock('inroute\Router\Environment'),
            [],
            []
        );

        $this->assertEquals('/path/123', $route->generate(array('id' => '123')));

        // Generate with param id missing
        $this->setExpectedException('RuntimeException');
        $route->generate([]);
    }

    public function testGetName()
    {
        $env = \Mockery::mock('inroute\Router\Environment');
        $env->shouldReceive('get')->once()->with('controller_name')->andReturn('name');
        $env->shouldReceive('get')->once()->with('controller_method')->andReturn('method');

        $route = new Route(
            [],
            \Mockery::mock('inroute\Router\Regex'),
            $env,
            [],
            []
        );

        $this->assertEquals('name::method', $route->getName());
    }
}

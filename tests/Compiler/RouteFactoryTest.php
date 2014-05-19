<?php
namespace inroute\Compiler;

use inroute\Router\Environment;

class RouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $env = \Mockery::mock('inroute\Router\Environment');
        $env->shouldReceive('get')->once()->with('path')->andReturn('returned-path');

        $definition = \Mockery::mock('inroute\Compiler\Definition');
        $definition->shouldReceive('getEnvironment')->times(2)->andReturn($env);
        $definition->shouldReceive('getPreFilters')->once()->andReturn([]);
        $definition->shouldReceive('getPostFilters')->once()->andReturn([]);

        $definitionFactory = \Mockery::mock('inroute\Compiler\DefinitionFactory');
        $definitionFactory->shouldReceive('getIterator')->once()->andReturn(new \ArrayIterator([$definition]));

        $tokenizer = \Mockery::mock('inroute\Compiler\Tokenizer');
        $tokenizer->shouldReceive('tokenize')->with('returned-path')->once()->andReturn([]);
        $tokenizer->shouldReceive('getRegex')->once()->andReturn(new \inroute\Router\Regex);

        $routeFactory = new RouteFactory($definitionFactory, $tokenizer);

        $result = iterator_to_array($routeFactory);
        $this->assertInstanceOf('inroute\Router\Route', $result[0]);
    }
}

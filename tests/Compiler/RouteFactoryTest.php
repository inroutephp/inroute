<?php
namespace inroute\Compiler;

class RouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $definition = \Mockery::mock('inroute\Compiler\Definition');
        $definition->shouldReceive('read')->times(4)->andReturn('path', array(), 'cntrl', 'method');
        $definition->shouldReceive('getPreFilters')->once()->andReturn([]);
        $definition->shouldReceive('getPostFilters')->once()->andReturn([]);

        $definitionFactory = \Mockery::mock('inroute\Compiler\DefinitionFactory');
        $definitionFactory->shouldReceive('getIterator')->once()->andReturn(new \ArrayIterator([$definition]));

        $tokenizer = \Mockery::mock('inroute\Compiler\Tokenizer');
        $tokenizer->shouldReceive('tokenize')->with('path')->once()->andReturn([]);
        $tokenizer->shouldReceive('getRegex')->once()->andReturn(new \inroute\Router\Regex);

        $routeFactory = new RouteFactory($definitionFactory, $tokenizer);

        $result = iterator_to_array($routeFactory);
        $this->assertInstanceOf('inroute\Router\Route', $result[0]);
    }
}

<?php
namespace inroute\Compiler;

class DefinitionIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $constructor = \Mockery::mock('ReflectionMethod');
        $constructor->shouldReceive('isConstructor')->once()->andReturn(true);

        $route = \Mockery::mock('ReflectionMethod');
        $route->shouldReceive('isConstructor')->once()->andReturn(false);
        $route->shouldReceive('getName')->once()->andReturn('routeName');
        $route->shouldReceive('getDocComment')->once();

        $class = \Mockery::mock('ReflectionClass');
        $class->shouldReceive('getName')->once()->andReturn('ControllerClassName');
        $class->shouldReceive('getDocComment')->once();
        $class->shouldReceive('getMethods')->once()->andReturn(array($constructor, $route));

        $result = iterator_to_array(new DefinitionIterator($class));

        $this->assertEquals(
            [
                'controller' => 'ControllerClassName',
                'controllerMethod' => 'routeName'
            ],
            $result[0]->toArray()
        );
    }
}

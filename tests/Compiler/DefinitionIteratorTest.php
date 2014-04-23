<?php
namespace inroute\Compiler;

class DefinitionIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $class = $this->getMockBuilder('ReflectionClass')
            ->disableOriginalConstructor()
            ->getMock();

        $class->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('ControllerClassName'));

        $constructor = $this->getMockBuilder('ReflectionMethod')
            ->disableOriginalConstructor()
            ->getMock();

        $constructor->expects($this->once())
            ->method('isConstructor')
            ->will($this->returnValue(true));

        $route = $this->getMockBuilder('ReflectionMethod')
            ->disableOriginalConstructor()
            ->getMock();

        $route->expects($this->once())
            ->method('getName')
            ->will($this->returnValue('routeName'));

        $class->expects($this->once())
            ->method('getMethods')
            ->will($this->returnValue(array($constructor, $route)));

        $return = iterator_to_array(new DefinitionIterator($class));

        $this->assertEquals(
            array(
                'controller' => 'ControllerClassName',
                'controllerMethod' => 'routeName'
            ),
            $return[0]->toArray()
        );
    }
}

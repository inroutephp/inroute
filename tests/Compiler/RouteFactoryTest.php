<?php
namespace inroute\Compiler;

class RouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $definition = $this->getMockBuilder('inroute\Compiler\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        $definition->expects($this->exactly(4))
            ->method('read')
            ->will($this->onConsecutiveCalls('path', array(), 'cntrl', 'method'));

        $definition->expects($this->once())
            ->method('getPreFilters')
            ->will($this->returnValue(array()));

        $definition->expects($this->once())
            ->method('getPostFilters')
            ->will($this->returnValue(array()));

        $definitionFactory = $this->getMockBuilder('inroute\Compiler\DefinitionFactory')
            ->disableOriginalConstructor()
            ->getMock();

        $definitionFactory->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array($definition))));

        $tokenizer = $this->getMock('inroute\Compiler\Tokenizer');

        $tokenizer->expects($this->once())
            ->method('tokenize')
            ->with('path')
            ->will($this->returnValue(array()));

        $tokenizer->expects($this->once())
            ->method('getRegex')
            ->will($this->returnValue(new \inroute\Router\Regex));

        $routeFactory = new RouteFactory($definitionFactory, $tokenizer);

        $result = iterator_to_array($routeFactory);
        $this->assertInstanceOf('inroute\Router\Route', $result[0]);
    }
}

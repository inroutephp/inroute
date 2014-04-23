<?php
namespace inroute\Compiler;

class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $classIterator = $this->getMock('inroute\Compiler\ClassIterator');

        $classIterator->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array(__CLASS__))));

        $plugin = $this->getMock('inroute\PluginInterface');

        $plugin->expects($this->atLeastOnce())
            ->method('processDefinition');

        $result = iterator_to_array(
            new DefinitionFactory($classIterator, $plugin)
        );

        $this->assertFalse(empty($result));
    }

    public function testCompilerSkipRouteException()
    {
        $classIterator = $this->getMock('inroute\Compiler\ClassIterator');

        $classIterator->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array(__CLASS__))));

        $plugin = $this->getMock('inroute\PluginInterface');

        $plugin->expects($this->atLeastOnce())
            ->method('processDefinition')
            ->will($this->throwException(new \inroute\Exception\CompilerSkipRouteException));

        $this->assertEmpty(
            iterator_to_array(
                new DefinitionFactory($classIterator, $plugin)
            )
        );
    }
}

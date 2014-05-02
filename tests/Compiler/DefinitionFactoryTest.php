<?php
namespace inroute\Compiler;

class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $controllerIterator = $this->getMockBuilder('inroute\classtools\ReflectionClassIteratorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $controllerIterator->expects($this->once())
            ->method('getIterator')
            ->will(
                $this->returnValue(
                    new \ArrayIterator(
                        array('Exception' => new \ReflectionClass('Exception'))
                    )
                )
            );

        $plugin = $this->getMock('inroute\PluginInterface');

        $plugin->expects($this->atLeastOnce())
            ->method('processDefinition');

        $result = iterator_to_array(
            new DefinitionFactory($controllerIterator, $plugin)
        );

        $this->assertFalse(empty($result));
    }

    public function testCompilerSkipRouteException()
    {
        $controllerIterator = $this->getMockBuilder('inroute\classtools\ReflectionClassIteratorInterface')
            ->disableOriginalConstructor()
            ->getMock();

        $controllerIterator->expects($this->once())
            ->method('getIterator')
            ->will(
                $this->returnValue(
                    new \ArrayIterator(
                        array('Exception' => new \ReflectionClass('Exception'))
                    )
                )
            );

        $plugin = $this->getMock('inroute\PluginInterface');

        $plugin->expects($this->atLeastOnce())
            ->method('processDefinition')
            ->will($this->throwException(new \inroute\Exception\CompilerSkipRouteException));

        $this->assertEmpty(
            iterator_to_array(
                new DefinitionFactory($controllerIterator, $plugin)
            )
        );
    }
}

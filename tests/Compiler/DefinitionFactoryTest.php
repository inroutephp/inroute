<?php
namespace inroute\Compiler;

class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $classIterator = $this->getMockBuilder('hanneskod\classtools\FilterableClassIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $classIterator->expects($this->once())
            ->method('filterType')
            ->with('inroute\ControllerInterface')
            ->will($this->returnValue($classIterator));
        $classIterator->expects($this->once())
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

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $result = iterator_to_array(
            new DefinitionFactory($classIterator, $plugin, $logger)
        );

        $this->assertFalse(empty($result));
    }

    public function testCompilerSkipRouteException()
    {
        $classIterator = $this->getMockBuilder('hanneskod\classtools\FilterableClassIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $classIterator->expects($this->once())
            ->method('filterType')
            ->with('inroute\ControllerInterface')
            ->will($this->returnValue($classIterator));
        $classIterator->expects($this->once())
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

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $this->assertEmpty(
            iterator_to_array(
                new DefinitionFactory($classIterator, $plugin, $logger)
            )
        );
    }
}

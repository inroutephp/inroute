<?php
namespace inroute\Compiler;

class DefinitionFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGetIterator()
    {
        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('filterType->where')->once()->andReturn(
            new \ArrayIterator(
                array('Exception' => new \ReflectionClass('Exception'))
            )
        );

        $plugin = \Mockery::mock('inroute\Plugin\PluginInterface');
        $plugin->shouldReceive('processDefinition')->zeroOrMoreTimes();

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->zeroOrMoreTimes();

        $result = iterator_to_array(
            new DefinitionFactory($classIterator, $plugin, $logger)
        );

        $this->assertFalse(empty($result));
    }

    public function testCompilerSkipRouteException()
    {
        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('filterType->where')->once()->andReturn(
            new \ArrayIterator(
                array('Exception' => new \ReflectionClass('Exception'))
            )
        );

        $plugin = \Mockery::mock('inroute\Plugin\PluginInterface');
        $plugin->shouldReceive('processDefinition')->andThrow(new \inroute\Exception\CompilerSkipRouteException);

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->zeroOrMoreTimes();
        $logger->shouldReceive('debug')->zeroOrMoreTimes();

        $this->assertEmpty(
            iterator_to_array(
                new DefinitionFactory($classIterator, $plugin, $logger)
            )
        );
    }
}

<?php
namespace inroute\Settings;

class SettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testMergingMultipleSettings()
    {
        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->zeroOrMoreTimes();

        $settingsA = $mock = \Mockery::mock('inroute\CompileSettingsInterface');
        $settingsA->shouldReceive('getRootPath')->once()->andReturn('foopath');
        $settingsA->shouldReceive('getPlugins')->once()->andReturn(['fooplugin']);

        $settingsB = $mock = \Mockery::mock('inroute\CompileSettingsInterface');
        $settingsB->shouldReceive('getRootPath')->once()->andReturn('barpath');
        $settingsB->shouldReceive('getPlugins')->once()->andReturn(['barplugin']);

        $reflectionClass = \Mockery::mock('ReflectionClass');
        $reflectionClass->shouldReceive('getConstructor->getNumberOfParameters')->twice()->andReturn(0);
        $reflectionClass->shouldReceive('newInstance')->twice()->andReturn($settingsA, $settingsB);
        $reflectionClass->shouldReceive('getName')->andReturn('MockedFreflectionClass');

        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('filterType->where')->once()->andReturn(
            new \ArrayIterator([$reflectionClass, $reflectionClass])
        );

        $manager = new SettingsManager($classIterator, $logger);

        $this->assertEquals('barpath', $manager->getRootPath());
        $this->assertEquals(['fooplugin', 'barplugin'], $manager->getPlugins());
    }

    public function testSkipNonInstantiableClass()
    {
        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('warning')->once();

        $reflectionClass = \Mockery::mock('ReflectionClass');
        $reflectionClass->shouldReceive('getConstructor->getNumberOfParameters')->once()->andReturn(1);
        $reflectionClass->shouldReceive('getName')->andReturn('MockedFreflectionClass');

        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('filterType->where')->once()->andReturn(
            new \ArrayIterator([$reflectionClass])
        );

        $manager = new SettingsManager($classIterator, $logger);
    }
}

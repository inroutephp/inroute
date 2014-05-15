<?php
namespace inroute\Settings;

class SettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testMergingMultipleSettings()
    {
        $reflectionClass = \Mockery::mock('ReflectionClass');
        $reflectionClass->shouldReceive('getName');

        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('filterType')->once()->andReturn(
            new \ArrayIterator([$reflectionClass, $reflectionClass])
        );

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->atLeast()->times(2);

        $settings = $mock = \Mockery::mock('inroute\Settings\CompileSettingsInterface');
        $settings->shouldReceive('getRootPath')->twice()->andReturn('barpath');
        $settings->shouldReceive('getPlugins')->twice()->andReturn(['barplugin']);

        $instantiator = \Mockery::mock('inroute\Settings\Instantiator');
        $instantiator->shouldReceive('setReflectionClass')->twice()->with($reflectionClass);
        $instantiator->shouldReceive('isInstantiableWithoutArgs')->twice()->andReturn(true);
        $instantiator->shouldReceive('instantiate')->twice()->andReturn($settings);

        $manager = new SettingsManager($classIterator, $logger, $instantiator);

        $this->assertEquals('barpath', $manager->getRootPath());
        $this->assertEquals(['barplugin', 'barplugin'], $manager->getPlugins());
    }

    public function testSkipNonInstantiableClass()
    {
        $reflectionClass = \Mockery::mock('ReflectionClass');
        $reflectionClass->shouldReceive('getName')->once();

        $classIterator = \Mockery::mock('hanneskod\classtools\FilterableClassIterator');
        $classIterator->shouldReceive('filterType')->once()->andReturn(
            new \ArrayIterator([$reflectionClass])
        );

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('warning')->once();

        $instantiator = \Mockery::mock('inroute\Settings\Instantiator');
        $instantiator->shouldReceive('setReflectionClass')->once()->with($reflectionClass);
        $instantiator->shouldReceive('isInstantiableWithoutArgs')->once()->andReturn(false);

        $manager = new SettingsManager($classIterator, $logger, $instantiator);
    }
}

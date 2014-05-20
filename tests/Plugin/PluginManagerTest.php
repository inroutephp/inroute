<?php
namespace inroute\Plugin;

class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessRouteDefinition()
    {
        $env = \Mockery::mock('inroute\Runtime\Environment');
        $env->shouldReceive('set')->atLeast()->times(1);

        $def = \Mockery::mock('inroute\Compiler\Definition');
        $def->shouldReceive('getEnvironment')->andReturn($env);

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->atLeast()->times(1);

        $plugin = \Mockery::mock('inroute\Plugin\PluginInterface');
        $plugin->shouldReceive('processRouteDefinition')->times(2)->with($def);
        $plugin->shouldReceive('setLogger')->times(2)->with($logger);

        $settings = \Mockery::mock('inroute\Settings\SettingsInterface');
        $settings->shouldReceive('getRootPath')->once();
        $settings->shouldReceive('getPlugins')->once()->andReturn([$plugin, $plugin]);

        $manager = new PluginManager($settings, $logger);
        $manager->processRouteDefinition($def);
    }
}

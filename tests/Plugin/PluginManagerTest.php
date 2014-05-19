<?php
namespace inroute\Plugin;

class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessDefinition()
    {
        $env = \Mockery::mock('inroute\Compiler\Environment');
        $env->shouldReceive('set')->atLeast()->times(1);

        $def = \Mockery::mock('inroute\Compiler\Definition');
        $def->shouldReceive('getEnvironment')->andReturn($env);

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->atLeast()->times(1);

        $plugin = \Mockery::mock('inroute\Plugin\PluginInterface');
        $plugin->shouldReceive('processDefinition')->times(2)->with($def);
        $plugin->shouldReceive('setLogger')->times(2)->with($logger);

        $settings = \Mockery::mock('inroute\Settings\CompileSettingsInterface');
        $settings->shouldReceive('getRootPath')->once();
        $settings->shouldReceive('getPlugins')->once()->andReturn([$plugin, $plugin]);

        $manager = new PluginManager($settings, $logger);
        $manager->processDefinition($def);
    }
}

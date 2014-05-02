<?php
namespace inroute\Plugin;

class PluginManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testProcessDefinition()
    {
        $definition = $this->getMockBuilder('inroute\Compiler\Definition')
            ->disableOriginalConstructor()
            ->getMock();

        $plugin = $this->getMock('inroute\PluginInterface');
        $plugin->expects($this->exactly(2))
            ->method('processDefinition')
            ->with($definition);

        $settings = $this->getMock('inroute\CompileSettingsInterface');
        $settings->expects($this->once())->method('getRootPath');
        $settings->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue(array($plugin, $plugin)));

        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->atLeastOnce())->method('info');

        $manager = new PluginManager($settings, $logger);
        $manager->processDefinition($definition);
    }
}

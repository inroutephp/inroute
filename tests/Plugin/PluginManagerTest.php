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

        $manager = new PluginManager(
            $plugin,
            $plugin
        );

        $manager->processDefinition($definition);
    }
}

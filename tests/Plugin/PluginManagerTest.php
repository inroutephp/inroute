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

        $logger = $this->getMock('Psr\Log\LoggerInterface');

        $manager = new PluginManager($logger);

        $manager->registerPlugin($plugin);
        $manager->registerPlugin($plugin);

        $manager->processDefinition($definition);
    }
}

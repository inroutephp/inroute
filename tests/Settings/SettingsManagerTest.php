<?php
namespace inroute\Settings;

class SettingsManagerTest extends \PHPUnit_Framework_TestCase
{
    public function testReadSettings()
    {
        $logger = $this->getMock('Psr\Log\LoggerInterface');
        $logger->expects($this->once())->method('info');

        $settingsClass = $this->getMock('inroute\CompileSettingsInterface');
        $settingsClass->expects($this->once())
            ->method('getRootPath')
            ->will($this->returnValue('barpath'));
        $settingsClass->expects($this->once())
            ->method('getPlugins')
            ->will($this->returnValue(array('fooplugin')));

        $reflectionClass = $this->getMockBuilder('ReflectionClass')
            ->disableOriginalConstructor()
            ->getMock();
        $reflectionClass->expects($this->once())
            ->method('newInstance')
            ->will($this->returnValue($settingsClass));

        $classIterator = $this->getMockBuilder('hanneskod\classtools\FilterableClassIterator')
            ->disableOriginalConstructor()
            ->getMock();
        $classIterator->expects($this->once())
            ->method('filterType')
            ->with('inroute\CompileSettingsInterface')
            ->will($this->returnValue($classIterator));
        $classIterator->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue(new \ArrayIterator(array($reflectionClass))));

        $manager = new SettingsManager($classIterator, $logger);

        $this->assertEquals('barpath', $manager->getRootPath());
        $this->assertEquals(array('fooplugin'), $manager->getPlugins());
    }
}

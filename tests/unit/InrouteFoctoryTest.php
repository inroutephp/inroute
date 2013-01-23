<?php
namespace itbz\inroute;

class InrouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $settings = array(
        "root" => "a",
        "prefixes" => "d",
        "dirs" => "e",
        "files" => "f",
        "classes" => "g"
    );

    public function testSetSettings()
    {
        $facade = new InrouteFactory();
        $facade->setRoot($this->settings['root']);
        $facade->setPrefixes($this->settings['prefixes']);
        $facade->setDirs($this->settings['dirs']);
        $facade->setFiles($this->settings['files']);
        $facade->setClasses($this->settings['classes']);
        $this->assertEquals($this->settings, $facade->getSettings());
    }

    public function testGenerate()
    {
        $generator = $this->getMock(
            '\itbz\inroute\CodeGenerator',
            array('addClass', 'generate'),
            array(),
            '',
            false
        );

        $scanner = $this->getMock(
            '\itbz\inroute\ClassScanner',
            array(),
            array(),
            '',
            false
        );

        $factory = new InrouteFactory($generator, $scanner);
        $factory->setDirs(array('dirname'));
        $factory->setFiles(array('filename'));

        $scanner->expects($this->once())
            ->method('addPrefix')
            ->with('php');

        $scanner->expects($this->once())
            ->method('addDir')
            ->with('dirname');

        $scanner->expects($this->once())
            ->method('addFile')
            ->with('filename');

        $scanner->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array('filename')));

        $generator->expects($this->atLeastOnce())
            ->method('addClass');

        $generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('output'));

        $this->assertEquals('output', $factory->generate());
    }
}

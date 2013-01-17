<?php
namespace itbz\inroute;

class InrouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    private $settings = array(
        "root" => "a",
        "caller" => "b",
        "container" => "c",
        "prefixes" => "d",
        "dirs" => "e",
        "files" => "f",
        "classes" => "g"
    );

    public function testLoadSettings()
    {
        $facade = new InrouteFactory();
        $facade->loadSettings($this->settings);
        $this->assertEquals($this->settings, $facade->getSettings());
    }

    public function testSetSettings()
    {
        $facade = new InrouteFactory();
        $facade->setRoot($this->settings['root']);
        $facade->setCaller($this->settings['caller']);
        $facade->setContainer($this->settings['container']);
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
            array('addClasses', 'generate'),
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

        $facade = new InrouteFactory($generator, $scanner);

        $facade->loadSettings(
            array(
                'prefixes' => 'php',
                'dirs' => 'dirname',
                'files' => 'filename'
            )
        );

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

        $generator->expects($this->at(2))
            ->method('addClasses');

        $generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('output'));

        $this->assertEquals('output', $facade->generate());
    }
}

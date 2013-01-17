<?php
namespace itbz\inroute;

class InrouteFacadeTest extends \PHPUnit_Framework_TestCase
{
    private $settings = array(
        "root" => "a",
        "caller" => "b",
        "container" => "c",
        "prefixes" => "d",
        "dirs" => "e",
        "files" => "f"
    );

    public function testLoadSettings()
    {
        $facade = new InrouteFacade();
        $facade->loadSettings($this->settings);
        $this->assertEquals($this->settings, $facade->getSettings());
    }

    public function testSetSettings()
    {
        $facade = new InrouteFacade();
        $facade->setRoot($this->settings['root']);
        $facade->setCaller($this->settings['caller']);
        $facade->setContainer($this->settings['container']);
        $facade->setPrefixes($this->settings['prefixes']);
        $facade->setDirs($this->settings['dirs']);
        $facade->setFiles($this->settings['files']);
        $this->assertEquals($this->settings, $facade->getSettings());
    }

    public function testFullStack()
    {
        $generator = $this->getMock(
            '\itbz\inroute\RouterGenerator',
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

        $facade = new InrouteFacade($generator, $scanner);

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

        $generator->expects($this->once())
            ->method('addClass')
            ->with('filename');

        $generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('output'));

        $this->assertEquals('output', $facade->generate());
    }
}

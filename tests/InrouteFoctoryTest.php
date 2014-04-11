<?php
namespace inroute;

class InrouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $scanner = $this->getMock('\inroute\ClassScanner');

        $scanner->expects($this->once())
            ->method('addDir')
            ->with('dirname');

        $scanner->expects($this->once())
            ->method('addFile')
            ->with('filename');

        $scanner->expects($this->once())
            ->method('getClasses')
            ->will($this->returnValue(array('filename')));

        $generator = $this->getMock(
            '\inroute\CodeGenerator',
            array('addClass', 'generate'),
            array(),
            '',
            false
        );

        $generator->expects($this->atLeastOnce())
            ->method('addClass');

        $generator->expects($this->once())
            ->method('generate')
            ->will($this->returnValue('output'));

        $factory = new InrouteFactory($generator, $scanner);
        $factory->addDirs(array('dirname'));
        $factory->addFiles(array('filename'));

        $this->assertEquals('output', $factory->generate());
    }

    public function testAddClasses()
    {
        $scanner = $this->getMock('\inroute\ClassScanner');

        $generator = $this->getMock(
            '\inroute\CodeGenerator',
            array('addClasses'),
            array(),
            '',
            false
        );

        $classes = array('foobar');

        $generator->expects($this->once())
            ->method('addClasses')
            ->with($classes);

        $factory = new InrouteFactory($generator, $scanner);
        $factory->addClasses($classes);
    }

    public function testSetRoot()
    {
        $scanner = $this->getMock('\inroute\ClassScanner');

        $generator = $this->getMock(
            '\inroute\CodeGenerator',
            array('setRoot'),
            array(),
            '',
            false
        );

        $generator->expects($this->once())
            ->method('setRoot')
            ->with('foobar');

        $factory = new InrouteFactory($generator, $scanner);
        $factory->setRoot('foobar');

    }
}

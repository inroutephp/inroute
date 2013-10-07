<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute;

class InrouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testGenerate()
    {
        $scanner = $this->getMock('\iio\inroute\ClassScanner');

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
            '\iio\inroute\CodeGenerator',
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
        $scanner = $this->getMock('\iio\inroute\ClassScanner');

        $generator = $this->getMock(
            '\iio\inroute\CodeGenerator',
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
        $scanner = $this->getMock('\iio\inroute\ClassScanner');

        $generator = $this->getMock(
            '\iio\inroute\CodeGenerator',
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

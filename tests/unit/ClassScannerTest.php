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

class ClassScannerTest extends \PHPUnit_Framework_TestCase
{
    public function testScanFile()
    {
        $scanner = new ClassScanner();
        $classes = $scanner
            ->addFile(__DIR__ . '/data/Working.php')
            ->addFile(__DIR__ . '/data/Working.php')
            ->getClasses();

        $this->assertEquals(
            array('unit\data\Working'),
            $classes,
            'Multiple scans should not yield multiple array entries'
        );
    }

    public function testScanDir()
    {
        $scanner = new ClassScanner();
        $classes = $scanner
            ->addDir(__DIR__ . '/data/')
            ->getClasses();

        $this->assertContains(
            'unit\data\Container',
            $classes
        );
    }

    /**
     * See Issue #15.
     * Scanning a class that extends a class not availiable at scan time.
     */
    public function testScanInheritedClass()
    {
        $scanner = new ClassScanner();
        $classes = $scanner
            ->addFile(__DIR__ . '/data/Extended.php')
            ->getClasses();

        $this->assertEquals(
            array('unit\data\Extended'),
            $classes
        );
    }
}

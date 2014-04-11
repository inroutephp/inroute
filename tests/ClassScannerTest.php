<?php
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
            array('data\Working'),
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
            'data\Container',
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
            array('data\Extended'),
            $classes
        );
    }
}

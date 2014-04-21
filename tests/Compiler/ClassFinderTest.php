<?php
namespace inroute\Compiler;

class ClassFinderTest extends \PHPUnit_Framework_TestCase
{
    public function testNoConstructArgs()
    {
        foreach (new ClassFinder as $class) {
            $this->assertTrue(false, 'This line should never happen');
        }
    }

    public function testInvalidConstructorArgs()
    {
        $this->setExpectedException('inroute\Exception\RuntimeException');
        new ClassFinder(array('not-a-file-or-dir'));
    }

    public function testScanFile()
    {
        $finder = new ClassFinder(
            array(
                __DIR__ . '/../data/Working.php',
                __DIR__ . '/../data/Working.php'
            )
        );

        $this->assertEquals(
            new \ArrayIterator(array('data\Working')),
            $finder->getIterator(),
            'Multiple scans should not yield multiple array entries'
        );
    }

    public function testScanDir()
    {
        $finder = new ClassFinder(array(__DIR__ . '/../data/'));
        $this->assertContains(
            'data\Working',
            $finder->getIterator()
        );
    }

    /**
     * See Issue #15.
     * Scanning a class that extends a class not availiable at scan time.
     */
    public function testScanInheritedClass()
    {
        $finder = new ClassFinder(array(__DIR__ . '/../data/Extended.php'));
        $this->assertEquals(
            new \ArrayIterator(array('data\Extended')),
            $finder->getIterator()
        );
    }
}

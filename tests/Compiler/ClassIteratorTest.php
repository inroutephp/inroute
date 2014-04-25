<?php
namespace inroute\Compiler;

class ClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testNoConstructArgs()
    {
        foreach (new ClassIterator as $class) {
            $this->assertTrue(false, 'This line should never happen');
        }
    }

    public function testInvalidConstructorArgs()
    {
        $this->setExpectedException('inroute\Exception\RuntimeException');
        new ClassIterator(array('not-a-file-or-dir'));
    }

    public function testScanFile()
    {
        $finder = new ClassIterator(
            array(
                __DIR__ . '/../../example/Working.php',
                __DIR__ . '/../../example/Working.php'
            )
        );

        $this->assertEquals(
            new \ArrayIterator(array('inroute\example\Working')),
            $finder->getIterator(),
            'Multiple scans should not yield multiple array entries'
        );
    }

    public function testScanDir()
    {
        $finder = new ClassIterator(array(__DIR__ . '/../../example/'));
        $this->assertContains(
            'inroute\example\Working',
            $finder->getIterator()
        );
    }

    /**
     * See Issue #15.
     * Scanning a class that extends a class not availiable at scan time.
     */
    public function testScanInheritedClass()
    {
        $finder = new ClassIterator(array(__DIR__ . '/../../example/Extended.php'));
        $this->assertEquals(
            new \ArrayIterator(array('inroute\example\Extended')),
            $finder->getIterator()
        );
    }
}

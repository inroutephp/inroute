<?php
namespace inroute\classtools;

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
        $classIterator = new ClassIterator(
            array(
                __DIR__ . '/../../example/Working.php',
                __DIR__ . '/../../example/Working.php'
            )
        );

        $this->assertEquals(
            new \ArrayIterator(
                array(
                    'inroute\example\Working' => new \ReflectionClass('inroute\example\Working')
                )
            ),
            $classIterator->getIterator(),
            'Multiple scans should not yield multiple array entries'
        );
    }

    public function testScanDir()
    {
        $classIterator = new ClassIterator(array(__DIR__ . '/../../example/'));
        $return = iterator_to_array($classIterator);
        $this->assertEquals(
            new \ReflectionClass('inroute\example\Working'),
            $return['inroute\example\Working']
        );
    }

    /**
     * See Issue #15.
     * Scanning a class that extends a class not availiable at scan time.
     * @todo No longer supported due to use of ReflectionClass, check issue...
     */
    public function testScanInheritedClass()
    {
        /*$finder = new ClassIterator(array(__DIR__ . '/../../example/Extended.php'));
        $this->assertEquals(
            new \ArrayIterator(array('inroute\example\Extended')),
            $finder->getIterator()
        );*/
    }
}

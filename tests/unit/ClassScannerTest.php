<?php
namespace itbz\inroute;

class ClassScannerTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @expectedException itbz\inroute\Exception\RuntimeExpection
     */
    public function testAddUnreadableFileException()
    {
        $finder = $this->getMock(
            'Symfony\Component\Finder\Finder',
            array(),
            array(),
            '',
            false
        );
        $scanner = new ClassScanner($finder);
        $scanner->addFile('foobar');
    }

    /**
     * @expectedException itbz\inroute\Exception\RuntimeExpection
     */
    public function testAddIncludedFileException()
    {
        $finder = $this->getMock(
            'Symfony\Component\Finder\Finder',
            array(),
            array(),
            '',
            false
        );
        $scanner = new ClassScanner($finder);
        // Include this file with is already included...
        $scanner->addFile(__DIR__ . '/ClassScannerTest.php');
    }

    public function testAddFile()
    {
        $finder = $this->getMock(
            'Symfony\Component\Finder\Finder',
            array('getIterator'),
            array(),
            '',
            false
        );

        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->throwException(new \LogicException));

        $scanner = new ClassScanner($finder);
        $classes = $scanner->addFile(getcwd() . '/itbz/test/Working.php')
            ->getClasses();

        $this->assertEquals(array('itbz\test\Working'), $classes);
    }

    public function testScan()
    {
        $finder = $this->getMock(
            'Symfony\Component\Finder\Finder',
            array('getIterator'),
            array(),
            '',
            false
        );

        $file = $this->getMock(
            'Symfony\Component\Finder\SplFileInfo',
            array('getRealpath'),
            array(),
            '',
            false
        );

        $file->expects($this->once())
            ->method('getRealpath')
            ->will($this->returnValue(getcwd() . '/itbz/test/NoConstructor.php'));

        $iterator = new \ArrayIterator(array($file));

        $finder->expects($this->once())
            ->method('getIterator')
            ->will($this->returnValue($iterator));

        $scanner = new ClassScanner($finder);
        $classes = $scanner->addPrefix('php')
            ->addDir(getcwd() . '/itbz/test/')
            ->getClasses();

        $this->assertEquals(array('itbz\test\NoConstructor'), $classes);
    }
}

<?php
namespace inroute\classtools;

class InterfaceIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testIteratorOnlyReturnsInterface()
    {
        $classIterator = $this->getMock('inroute\classtools\ReflectionClassIteratorInterface');

        $classIterator->expects($this->once())
            ->method('getIterator')
            ->will(
                $this->returnValue(
                    new \ArrayIterator(
                        array(
                            'Exception' => new \ReflectionClass('Exception'),
                            'inroute\ControllerInterface' => new \ReflectionClass('inroute\ControllerInterface')
                        )
                    )
                )
            );

        $this->assertEquals(
            array('inroute\ControllerInterface' => new \ReflectionClass('inroute\ControllerInterface')),
            iterator_to_array(new InterfaceIterator('inroute\ControllerInterface', $classIterator))
        );
    }
}

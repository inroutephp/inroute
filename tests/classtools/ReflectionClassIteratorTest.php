<?php
namespace inroute\classtools;

class ReflectionClassIteratorTest extends \PHPUnit_Framework_TestCase
{
    public function testScanFile()
    {
        $result = iterator_to_array(new ReflectionClassIterator(__FILE__));

        $this->assertEquals(
            new \ReflectionClass(__CLASS__),
            $result[__CLASS__]
        );
    }
}

<?php
namespace itbz\inroute;

class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    public function testIsinroute()
    {
        $no = new ReflectionClass('itbz\test\NoInroute');
        $this->assertFalse($no->isInroute());

        $yes = new ReflectionClass('itbz\test\NoConstructor');
        $this->assertTrue($yes->isInroute());
    }

    public function testNoConstructor()
    {
        $refl = new ReflectionClass('itbz\test\NoConstructor');
        $this->assertEquals(
            array(),
            $refl->getInjections(),
            'List of injections for class with no constructor should be an empty array'
        );
    }

    /**
     * @expectedException itbz\inroute\Exception\InjectionException
     */
    public function testInjectionMissing()
    {
        $refl = new ReflectionClass('itbz\test\InjectionMissing');
        $refl->getInjections();
    }

    /**
     * @expectedException itbz\inroute\Exception\InjectionException
     */
    public function testParamMissing()
    {
        $refl = new ReflectionClass('itbz\test\InjectedParameterMissing');
        $refl->getInjections();
    }
}

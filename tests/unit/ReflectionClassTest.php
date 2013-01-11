<?php
namespace itbz\inroute;

class NoInroute
{
}

/**
 * @inroute
 */
class InrouteNoConstruct
{
}

/**
 * @inroute
 */
class InrouteInjectionMissing
{
    public function __construct($a)
    {
    }
}

/**
 * @inroute
 */
class InrouteParamMissing
{
    /**
     * @inject $b foobar
     */
    public function __construct($a)
    {
    }
}

class ReflectionClassTest extends \PHPUnit_Framework_TestCase
{
    public function testIsinroute()
    {
        $no = new ReflectionClass('itbz\inroute\NoInroute');
        $this->assertFalse($no->isInroute());

        $yes = new ReflectionClass('itbz\inroute\InrouteNoConstruct');
        $this->assertTrue($yes->isInroute());
    }

    public function testNoConstructor()
    {
        $refl = new ReflectionClass('itbz\inroute\InrouteNoConstruct');
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
        $refl = new ReflectionClass('itbz\inroute\InrouteInjectionMissing');
        $refl->getInjections();
    }

    /**
     * @expectedException itbz\inroute\Exception\InjectionException
     */
    public function testParamMissing()
    {
        $refl = new ReflectionClass('itbz\inroute\InrouteParamMissing');
        $refl->getInjections();
    }
}

<?php
namespace unit\data;

use iio\inroute\Route;

/**
 * @controller </root>
 */
class Working
{
    /**
     * @param array    $x   inject:xfactory
     * @param DateTime $bar inject:foobar
     * @param string   $y   INJECT:xx
     */
    public function __construct(\DateTime $bar, array $x, $y = null, $z = 'optional')
    {
        var_dump($bar);
        var_dump($x);
        var_dump($y);
        var_dump($z);
    }

    /**
     * It works even though the spacing is weird 
     * @route     GET   </foo/{:name}>
     */
    public function foo(Route $route)
    {
        return $route->generate('Working::bar');
    }

    /**
     * Multiple routes
     * 
     * @route POST </bar/{:name}>
     * @route POST </baar/{:name}>
     */
    public function bar()
    {
    }

    public function noRoute()
    {
    }
}

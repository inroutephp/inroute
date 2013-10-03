<?php
namespace unit\data;

use iio\inroute\Route;

/**
 * @inroute
 */
class Working
{
    /**
     * @param array    $x   inject:xfactory
     * @param DateTime $bar inject:foobar
     * @param string   $y   INJECT:xx
     */
    public function __construct(\DateTime $bar, array $x, $y = 'optional')
    {
        var_dump($bar);
        var_dump($x);
        var_dump($y);
    }

    /**
     * It works even though the spacing is weird 
     * @route     GET   /foo/{:name}
     */
    public function foo()
    {
        return 'Working::foo';
    }

    /**
     * Multiple routes
     * 
     * @route POST /bar/{:name}
     * @route POST /baar/{:name}
     */
    public function bar(Route $route)
    {
        var_dump($route);
    }

    public function noRoute()
    {
    }
}

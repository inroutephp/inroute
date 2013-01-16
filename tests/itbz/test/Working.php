<?php
namespace itbz\test;

use itbz\inroute\Route;

/**
 * @inroute
 */
class Working
{
    /**
     * @inject $x xfactory
     * @inject $bar foobar
     * @inject $y xx
     */
    public function __construct(\DateTime $bar, array $x, $y = 'optional')
    {
    }

    /**
     * @route GET /foo/{:name}
     */
    public function foo(Route $route)
    {
        return 'Working::foo';
    }

    /**
     * @route POST /bar/{:name}
     */
    public function bar(Route $route)
    {
        var_dump($route);
    }

    public function noRoute()
    {
    }
}

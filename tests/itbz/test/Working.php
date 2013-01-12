<?php
namespace itbz\test;

/**
 * @inroute
 */
class Working
{
    /**
     * @inject $x xfactory
     * @inject $bar foobar
     */
    public function __construct(\DateTime $bar, $x, $y = 'optional')
    {
    }

    /**
     * @route GET /domain/{:name}
     */
    public function foo(Route $route)
    {
        var_dump($route);
    }

    /**
     * @route POST /domain/{:name}
     */
    public function bar(Route $route)
    {
        var_dump($route);
    }

    public function noRoute()
    {
    }
}

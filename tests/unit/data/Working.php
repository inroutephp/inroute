<?php
namespace unit\data;

use iio\inroute\Route;

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
        var_dump($bar);
        var_dump($x);
        var_dump($y);
    }

    /**
     * @route GET /foo/{:name}
     */
    public function foo()
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

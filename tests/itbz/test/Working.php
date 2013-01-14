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
     * @inject $y xx
     */
    public function __construct(\DateTime $bar, array $x, $y = 'optional')
    {
    }

    /**
     * @route GET /domain/{:name}
     */
    public function foo()
    {
        return 'Working::foo';
    }

    /**
     * @route POST /domain/{:name}
     */
    public function bar($route)
    {
        var_dump($route);
    }

    public function noRoute()
    {
    }
}

<?php
namespace itbz\test;

use itbz\inroute\Route;

/**
 * @inroute
 */
class Controller
{
    /**
     * @inject $arg xx
     */
    public function __construct($arg = 'optional')
    {
        $this->arg = $arg;
    }

    /**
     * @route GET /foo/{:name}
     */
    public function view()
    {
        return 'Working::foo ' . $this->arg;
    }
}

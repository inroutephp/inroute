<?php
namespace inroute\example;

use inroute\Router\Route;

/**
 * The @controller annotation tells inroute that this class should be scanned
 * 
 * @controller </root>
 */
class Working
{
    /**
     * The route annotation tells inroute when to route requests to this method
     *
     * It works even though the spacing is weird
     *
     * @route     GET   </foo/{:name}>
     */
    public function foo(Route $route)
    {
        // Read path paramterer
        return $route->name;
        return $route->generate('Working::bar');
    }

    /**
     * Controller using multiple methods
     *
     * @route GET,POST </postAndGet>
     */
    public function bar()
    {
    }

    public function noRoute()
    {
    }
}

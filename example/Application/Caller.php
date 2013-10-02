<?php

use iio\inroute\Route;

/**
 * The @inrouteCaller annotation tells inroute that this caller should be used
 *
 * TIP: The DI-container is injected into the caller at construct for later use.
 * 
 * @inrouteCaller
 */
class Caller implements iio\inroute\CallerInterface
{
    public function call($controller, Route $route)
    {
        // If you want to create more objects at dispatch
        // this is the place
        $request = "custom created request object";
        return call_user_func($controller, $route, $request);

        // This is how the default caller is implemented
        // the controller method is called with the Route object
        // if this is what you want you can skip defining you own Caller
        return call_user_func($controller, $route);
    }
}

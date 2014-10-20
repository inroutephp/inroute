<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Runtime;

/**
 * Defines a route post filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface PostFilterInterface
{
    /**
     * Filter the route return value
     *
     * Post filters must implement the PostFilterInterface and are executed at
     * runtime in the order they are registered.
     *
     * A post filter is called with the return value of the route as
     * argument, and its return value is treated as a replacement.
     *
     * If the filter concludes that control should be passed to next executable
     * route a NextRouteException can be thrown.
     *
     * @param  mixed $returnValue
     * @return mixed
     * @throws NextRouteException If Control should be passed to next route
     */
    public function filter($returnValue);
}

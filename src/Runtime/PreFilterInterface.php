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
 * Defines a route pre filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface PreFilterInterface
{
    /**
     * Filter environment before route is executed
     *
     * Pre filters must implement the PreFilterInterface and are executed at
     * runtime in the order they are registered.
     * 
     * The return value of a pre filter is discarded, instead the filter should
     * alter the contents of the route environment.
     *
     * If the filter concludes that control should be passed to next executable
     * route a NextRouteException can be thrown.
     *
     * @param  Environment $env
     * @return void
     * @throws NextRouteException If Control should be passed to next route
     */
    public function filter(Environment $env);
}

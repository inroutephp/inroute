<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute;

/**
 * Interface for calling a system controller
 *
 * Custom callers must implement this interface
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface CallerInterface
{
    /**
     * Call a system controller
     *
     * @param  mixed $controller Anything acceptable by call_user_func
     * @param  Route $route
     * @return mixed Whatewer the controller returns
     */
    public function call($controller, Route $route);
}

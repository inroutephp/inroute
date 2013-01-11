<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2012 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package itbz\inroute
 */

namespace itbz\inroute;

/**
 * Interface for calling a system controller
 *
 * Custom callers must implement this interface
 *
 * @package itbz\inroute
 */
interface Caller
{
    /**
     * Call a system controller
     *
     * @param mixed $controller Anything acceptable by call_user_func
     * @param Route $route
     *
     * @return void
     */
    public function call($controller, Route $route);
}

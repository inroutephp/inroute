<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace itbz\inroute;

/**
 * Interface for calling a system controller
 *
 * Custom callers must implement this interface
 *
 * @package inroute
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */
interface CallerInterface
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

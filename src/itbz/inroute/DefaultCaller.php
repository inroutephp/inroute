<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package itbz\inroute
 */

namespace itbz\inroute;

/**
 * Default class for calling a system controller
 *
 * Sends the raw Route object to the controller
 *
 * @package itbz\inroute
 */
class DefaultCaller implements CallerInterface
{
    /**
     * {@inheritdoc}
     *
     * @param mixed $controller Anything acceptable by call_user_func
     * @param Route $route
     *
     * @return void
     */
    public function call($controller, Route $route)
    {
        call_user_func($controller, $route);
    }
}

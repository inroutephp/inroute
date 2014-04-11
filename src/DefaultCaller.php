<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute;

use Pimple;

/**
 * Call system controller
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefaultCaller implements CallerInterface
{
    /**
     * @var Pimple DI-container
     */
    private $container;

    /**
     * Call system controller
     *
     * @param Pimple $container
     */
    public function __construct(Pimple $container)
    {
        $this->container = $container;
    }

    /**
     * Get DI-container
     *
     * @return Pimple
     */
    public function getContainer()
    {
        return $this->container;
    }

    /**
     * {@inheritdoc}
     *
     * Sends the raw Route object to the controller.
     *
     * @param  mixed $controller Anything acceptable by call_user_func
     * @param  Route $route
     * @return mixed Whatewer the controller returns
     */
    public function call($controller, Route $route)
    {
        return call_user_func($controller, $route);
    }
}

<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute;

use Pimple;

/**
 * Call system controller
 *
 * Sends the raw Route object to the controller.
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
     * @param  mixed $controller Anything acceptable by call_user_func
     * @param  Route $route
     * @return mixed Whatewer the controller returns
     */
    public function call($controller, Route $route)
    {
        return call_user_func($controller, $route);
    }
}

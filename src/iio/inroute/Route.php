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

use Aura\Router\Route as AuraRoute;
use Aura\Router\Map;

/**
 * The Inrout route object
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Route
{
    /**
     * @var AuraRoute Aura route object
     */
    private $route;

    /**
     * @var Map Aura map object
     */
    private $map;

    /**
     * Construct
     *
     * @param AuraRoute $route
     * @param Map       $map
     */
    public function __construct(AuraRoute $route, Map $map)
    {
        $this->route = $route;
        $this->map = $map;
    }

    /**
     * Generate url based an route name and values
     *
     * @param  string $name Name of route to generate. If omitted current route is used
     * @param  array  $data data used in route path
     * @return string
     */
    public function generate($name = null, array $data = null)
    {
        if ($name) {
            return @$this->map->generate($name, $data);
        } else {
            return @$this->route->generate();
        }
    }

    /**
     * Get route name
     *
     * @return string
     */
    public function getName()
    {
        return $this->route->name;
    }

    /**
     * Get route path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->route->path;
    }

    /**
     * Ger route values
     *
     * @param string $key
     *
     * @return string
     */
    public function getValue($key)
    {
        return $this->route->values[$key];
    }

    /**
     * Ger route http methods
     *
     * @return string
     */
    public function getMethods()
    {
        return $this->route->values['method'];
    }
}

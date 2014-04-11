<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace iio\inroute;

use Aura\Router\Map;
use iio\inroute\Exception\RuntimeExpection;

/**
 * Inroute base class
 *
 * This class should not be instantiated directly. Instead use CodeGenerator
 * to generate code that returns a customized Inroute object.
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Inroute
{
    /**
     * @var Map Aura map object
     */
    private $map;

    /**
     * Constructor
     *
     * @param Map $map
     */
    public function __construct(Map $map)
    {
        $this->map = $map;
    }

    /**
     * Dispatch request
     *
     * @param  string           $path   The path to dispatch
     * @param  array            $server Usually $_SERVER
     * @return mixed            Whatever the caller returns
     * @throws RuntimeExpection If no route is found
     */
    public function dispatch($path, array $server)
    {
        assert('is_string($path)');
        $auraroute = $this->map->match($path, $server);

        if (!$auraroute) {
            $msg = "No route found for $path";
            throw new RuntimeExpection($msg);
        }

        $route = new Route($auraroute, $this->map);

        return $auraroute->values['controller']($route);
    }
}

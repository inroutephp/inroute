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

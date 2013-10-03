<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute\Tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use iio\inroute\Exception;

/**
 * Route annotation class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RouteTag
{
    /**
     * Array of valid HTTP methods
     */
    static private $validMethods = array(
        'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PATCH'
    );

    /**
     * @var array Http methods specified
     */
    private $methods;

    /**
     * @var string Route path
     */
    private $path;

    /**
     * Route annotation class
     *
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $parts = array_filter(preg_split('/\s+/', $tag->getDescription()));

        if (count($parts) < 2) {
            $msg = "Unable to create route from tag @route {$tag->getDescription()}";
            throw new Exception($msg);
        }
        
        $this->methods = explode(',', $parts[0]);

        foreach ($this->methods as $method) {
            if (!in_array($method, self::$validMethods)) {
                $msg = "Unable to create route using http method $method";
                throw new Exception($msg);
            }
        }

        $this->path = $parts[1];
    }

    /**
     * Get list of HTTP methods
     *
     * @return array
     */
    public function getMethods()
    {
        return $this->methods;
    }

    /**
     * Get route path
     *
     * @return string
     */
    public function getPath()
    {
        return $this->path;
    }
}

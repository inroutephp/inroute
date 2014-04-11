<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Tag;

use phpDocumentor\Reflection\DocBlock\Tag;
use inroute\Exception;

/**
 * Route annotation class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RouteTag extends AbstractTag
{
    /**
     * @var string Name of this tag
     */
    public static $name = 'route';

    /**
     * Array of valid HTTP methods
     */
    private static $validMethods = array(
        'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PATCH'
    );

    /**
     * @var array Http methods specified
     */
    private $methods;

    /**
     * Route annotation class
     *
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        parent::__construct($tag);

        $this->methods = explode(',', $this->parts[0]);

        foreach ($this->methods as $method) {
            if (!in_array($method, self::$validMethods)) {
                $msg = "Unable to create route using http method $method";
                throw new Exception($msg);
            }
        }
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
}

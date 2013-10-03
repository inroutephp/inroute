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

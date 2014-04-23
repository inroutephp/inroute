<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Plugin;

use inroute\PluginInterface;
use inroute\Compiler\Definition;

/**
 * Inroute core plugin
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Core implements PluginInterface
{
    /**
     * Array of valid HTTP methods
     */
    private static $validMethods = array(
        'GET', 'HEAD', 'POST', 'PUT', 'DELETE', 'TRACE', 'OPTIONS', 'PATCH'
    );

    private $root;

    /**
     * @param string $root Root path prepended to all paths
     */
    public function __construct($root = '')
    {
        $this->root = $root;
    }

    public function processDefinition(Definition $definition)
    {
        // TODO validera http metod
        // TODO validera att path finns med...
        // TODO CompilerSkipRouteException
            // om class ej @controller
            // om method ej @route

        $definition->path = $this->root
            . $definition->getClassAnnotation('controller')
            . $definition->getMethodAnnotation('route');

        /*
        // Från RouteTag
        foreach ($this->methods as $method) {
            if (!in_array($method, self::$validMethods)) {
                $msg = "Unable to create route using http method $method";
                throw new Exception($msg);
            }
        }
        */
    }
}

<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use inroute\PluginInterface;
use inroute\Router\Route;
use Closure;

/**
 * Create route objects from reflected controller classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RouteFactory
{
    private $caller, $tokenizer, $plugins = array(), $routes = array();

    /**
     * @param Closure   $caller    Closure used when invoking routes
     * @param Tokenizer $tokenizer Tokenizer used when parsing paths
     */
    public function __construct(Closure $caller, Tokenizer $tokenizer = null)
    {
        $this->caller = $caller;
        $this->tokenizer = $tokenizer ?: new Tokenizer;
    }

    /**
     * Load inroute plugin
     *
     * @param  PluginInterface $plugin
     * @return void
     */
    public function loadPlugin(PluginInterface $plugin)
    {
        $this->plugins[] = $plugin;
    }

    /**
     * Create routes from route descriptions
     *
     * @param  DefinitionFinderOld $definitions
     * @return void
     */
    public function addRoutes(DefinitionFinderOld $definitions)
    {
        foreach ($definitions as $def) {
            $this->routes[] = new Route(
                $this->tokenizer->tokenize($def['path']),
                $this->tokenizer->getRegex(),
                $def['httpmethods'],
                $def['controller'],
                $def['controllerMethod'],
                $this->caller
            );
        }
    }

    /**
     * Get routes definied in controller
     *
     * @return array<Route>
     */
    public function getRoutes()
    {
        return $this->routes;
    }
}

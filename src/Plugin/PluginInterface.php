<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Plugin;

use Psr\Log\LoggerAwareInterface;
use inroute\Compiler\Definition;
use inroute\Exception\CompilerSkipRouteException;

/**
 * Defines an inroute plugin
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface PluginInterface extends LoggerAwareInterface
{
    /**
     * Edit a route definition
     *
     * At compile time each found route definition is processed be registered
     * plugins. Plugins can read annotations from the controller, alter the
     * definition of the route and register pre and post filters.
     *
     * If a plugin concludes that this route should not be included in the
     * router a CompilerSkipRouteException can be thrown.
     *
     * @param  Definition $definition
     * @return void
     * @throws CompilerSkipRouteException If definition should be ignored
     */
    public function processRouteDefinition(Definition $definition);
}

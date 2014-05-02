<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute;

use inroute\Log\LoggerAwareInterface;
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
     * @param  Definition $definition
     * @return void
     * @throws CompilerSkipRouteException If definition should be ignored
     */
    public function processDefinition(Definition $definition);
}

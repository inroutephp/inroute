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
 * Handle a collection of plugins
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class PluginManager implements PluginInterface
{
    private $plugins;

    /**
     * @param PluginInterface[] $plugins
     */
    public function __construct(array $plugins)
    {
        $this->plugins = $plugins;
    }

    public function processDefinition(Definition $definition)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->processDefinition($definition);
        }
    }
}

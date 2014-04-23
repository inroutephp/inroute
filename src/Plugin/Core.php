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
        $definition->path = $this->root
            . $definition->getClassAnnotation('controller')
            . $definition->getMethodAnnotation('route');
    }
}

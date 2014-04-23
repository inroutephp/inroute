<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use IteratorAggregate;
use ReflectionClass;
use inroute\PluginInterface;
use inroute\Exception\CompilerSkipRouteException;

/**
 * Create route definitions from controller classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionFactory implements IteratorAggregate
{
    private $classes, $plugin;

    /**
     * @param ClassIterator   $classes
     * @param PluginInterface $plugin
     */
    public function __construct(ClassIterator $classes, PluginInterface $plugin)
    {
        $this->classes = $classes;
        $this->plugin = $plugin;
    }

    /**
     * @return \Iterator
     * @todo   Implement as a generator
     */
    public function getIterator()
    {
        $definitions = array();

        /** @var string $className */
        foreach ($this->classes as $className) {
            /** @var Definition $definition */
            foreach (new DefinitionIterator(new ReflectionClass($className)) as $definition) {
                try {
                    $this->plugin->processDefinition($definition);
                    $definitions[] = $definition;
                } catch (CompilerSkipRouteException $e) {
                    // Ignore definition on CompilerSkipRouteException
                }
            }
        }

        return new \ArrayIterator($definitions);
    }
}

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
use inroute\classtools\ReflectionClassIterator;
use inroute\PluginInterface;
use Psr\Log\LoggerInterface;
use inroute\Exception\CompilerSkipRouteException;

/**
 * Create route definitions from controller classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionFactory implements IteratorAggregate
{
    private $classIterator, $plugin, $logger;

    /**
     * @param ReflectionClassIterator $classIterator
     * @param PluginInterface         $plugin
     * @param LoggerInterface         $logger
     */
    public function __construct(ReflectionClassIterator $classIterator, PluginInterface $plugin, LoggerInterface $logger)
    {
        $this->classIterator = $classIterator->filterType('inroute\ControllerInterface');
        $this->plugin = $plugin;
        $this->logger = $logger;
    }

    /**
     * @return \Iterator
     * @todo   Implement as a generator
     */
    public function getIterator()
    {
        $definitions = array();

        foreach ($this->classIterator as $classname => $reflectedClass) {
            $this->logger->info("Reading routes from $classname");
            /** @var Definition $definition */
            foreach (new DefinitionIterator($reflectedClass) as $definition) {
                try {
                    $this->plugin->processDefinition($definition);
                    $definitions[] = $definition;
                    $this->logger->info("Found route {$definition->read('controllerMethod')}", $definition->toArray());
                } catch (CompilerSkipRouteException $e) {
                    $this->logger->debug("Skipped route {$definition->read('controllerMethod')}");
                }
            }
        }

        return new \ArrayIterator($definitions);
    }
}

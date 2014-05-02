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
use inroute\Log\LoggerAwareInterface;
use inroute\Log\LoggerAwareTrait;
use inroute\classtools\ReflectionClassIterator;
use inroute\PluginInterface;
use Psr\Log\LoggerInterface;
use inroute\Exception\CompilerSkipRouteException;

/**
 * Create route definitions from controller classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionFactory implements IteratorAggregate //, LoggerAwareInterface
{
    private $classIterator, $plugin;

    /**
     * @param ReflectionClassIterator $classIterator
     * @param PluginInterface         $plugin
     * @param LoggerInterface         $logger
     */
    public function __construct(ReflectionClassIterator $classIterator, PluginInterface $plugin, LoggerInterface $logger = null)
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

        foreach ($this->classIterator as $className => $reflectedClass) {
            // TODO logg controller
            //$this->getLogger()->info("Reading routes from $className");
            /** @var Definition $definition */
            foreach (new DefinitionIterator($reflectedClass) as $definition) {
                try {
                    $this->plugin->processDefinition($definition);
                    $definitions[] = $definition;
                    // TODO logg route
                    //$this->getLogger()->info("Found route {$definition->controllerMethod}", $definition->toArray());
                } catch (CompilerSkipRouteException $e) {
                    // Ignore definition on CompilerSkipRouteException
                    // TODO logg skipped route
                    //$this->getLogger()->debug("Skipped route {$definition->controllerMethod}");
                }
            }
        }

        return new \ArrayIterator($definitions);
    }
}

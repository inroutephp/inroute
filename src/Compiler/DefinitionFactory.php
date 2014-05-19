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
use hanneskod\classtools\FilterableClassIterator;
use Psr\Log\LoggerInterface;
use inroute\Plugin\PluginInterface;
use inroute\Exception\CompilerSkipRouteException;

/**
 * Create route definitions from controller classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionFactory implements IteratorAggregate
{
    /**
     * @var FilterableClassIterator Classes to search for routes
     */
    private $classIterator;

    /**
     * @var PluginInterface Plugin used to process found routes
     */
    private $plugin;

    /**
     * @var LoggerInterface Event logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param FilterableClassIterator $classIterator
     * @param PluginInterface         $plugin
     * @param LoggerInterface         $logger
     */
    public function __construct(
        FilterableClassIterator $classIterator,
        PluginInterface $plugin,
        LoggerInterface $logger
    ) {
        $this->classIterator = $classIterator
            ->filterType('inroute\Router\ControllerInterface')
            ->where('isInstantiable');
        $this->plugin = $plugin;
        $this->logger = $logger;
    }

    /**
     * Implementation of IteratorAggregate
     *
     * @return \Iterator
     */
    public function getIterator()
    {
        foreach ($this->classIterator as $classname => $reflectedClass) {
            $this->logger->info("Reading routes from <$classname>");
            /** @var Definition $def */
            foreach (new DefinitionIterator($reflectedClass) as $def) {
                try {
                    $this->plugin->processDefinition($def);
                    $this->logger->info("Found route <{$def->getEnvironment()->get('controller_method')}>");
                    yield $def;
                } catch (CompilerSkipRouteException $e) {
                    $this->logger->debug("Skipped route <{$def->getEnvironment()->get('controller_method')}>");
                }
            }
        }
    }
}

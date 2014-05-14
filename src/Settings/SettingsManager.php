<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Settings;

use inroute\CompileSettingsInterface;
use hanneskod\classtools\FilterableClassIterator;
use Psr\Log\LoggerInterface;

/**
 * Merge settings from multiple CompileSettingsInterface objects
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class SettingsManager implements CompileSettingsInterface
{
    /**
     * @var string Project root path, value from last parsed settings interface is used
     */
    private $root = '';

    /**
     * @var \inroute\PluginInterface[] Array of loaded plugins
     */
    private $plugins = array();

    /**
     * Merge settings from class iterator
     *
     * @param FilterableClassIterator $classIterator
     * @param LoggerInterface         $logger
     */
    public function __construct(FilterableClassIterator $classIterator, LoggerInterface $logger)
    {
        $iterator = $classIterator->filterType('inroute\CompileSettingsInterface')->where('isInstantiable');
        foreach ($iterator as $reflectedClass) {
            if ($reflectedClass->getConstructor()->getNumberOfParameters() > 0) {
                $logger->warning("Unable to instantiate <{$reflectedClass->getName()}>, constructor should not take parameters.");
                continue;
            }

            $logger->info("Reading build settings from <{$reflectedClass->getName()}>.");
            $settings = $reflectedClass->newInstance(0);
            $this->root = $settings->getRootPath();
            $logger->info("Using project root path <{$this->root}>.");

            $this->plugins = array_merge(
                $this->plugins,
                $settings->getPlugins()
            );
        }
    }

    /**
     * Get project root path
     *
     * @return string
     */
    public function getRootPath()
    {
        return $this->root;
    }

    /**
     * Get plugins to load
     *
     * @return \inroute\PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}

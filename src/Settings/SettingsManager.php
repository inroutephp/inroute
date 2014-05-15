<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Settings;

use hanneskod\classtools\FilterableClassIterator;
use Psr\Log\LoggerInterface;
use ReflectionClass;

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
     * @var \inroute\Plugin\PluginInterface[] Array of loaded plugins
     */
    private $plugins = array();

    /**
     * Merge settings from class iterator
     *
     * @param FilterableClassIterator $iterator
     * @param LoggerInterface         $logger
     * @param Instantiator            $instantiator
     */
    public function __construct(FilterableClassIterator $iterator, LoggerInterface $logger, Instantiator $instantiator)
    {
        foreach ($iterator->filterType('inroute\Settings\CompileSettingsInterface') as $reflectedClass) {
            $instantiator->setReflectionClass($reflectedClass);

            if (!$instantiator->isInstantiableWithoutArgs()) {
                $logger->warning("Unable to instantiate <{$reflectedClass->getName()}>");
                continue;
            }

            $logger->info("Reading build settings from <{$reflectedClass->getName()}>");
            $settings = $instantiator->instantiate();

            $this->root = $settings->getRootPath();

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
     * @return \inroute\Plugin\PluginInterface[]
     */
    public function getPlugins()
    {
        return $this->plugins;
    }
}

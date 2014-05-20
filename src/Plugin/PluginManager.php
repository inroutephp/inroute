<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Plugin;

use inroute\Settings\SettingsInterface;
use inroute\Compiler\Definition;
use Psr\Log\LoggerInterface;

/**
 * Handle a collection of plugins
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class PluginManager implements PluginInterface
{
    /**
     * @var LoggerInterface Event logger
     */
    private $logger;

    /**
     * @var PluginInterface[] Registered plugins
     */
    private $plugins = array();

    /**
     * Constructor
     *
     * @param SettingsInterface $settings
     * @param LoggerInterface   $logger
     */
    public function __construct(SettingsInterface $settings, LoggerInterface $logger)
    {
        $this->setLogger($logger);

        $path = $settings->getRootPath();
        $logger->info("Using project root path <{$path}>");

        $this->registerPlugin(new Core($path));

        foreach ($settings->getPlugins() as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    /**
     * Register plugin with manager
     *
     * @param  PluginInterface $plugin
     * @return void
     */
    public function registerPlugin(PluginInterface $plugin)
    {
        $this->logger->info("Plugin <".get_class($plugin)."> registered");
        $plugin->setLogger($this->logger);
        $this->plugins[] = $plugin;
    }

    /**
     * Implementation of PluginInterface
     *
     * @param  Definition $definition
     * @return void
     */
    public function processRouteDefinition(Definition $definition)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->processRouteDefinition($definition);
        }
    }

    /**
     * Implementation of PluginInterface
     *
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}

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
use inroute\CompileSettingsInterface;
use inroute\Compiler\Definition;
use Psr\Log\LoggerInterface;

/**
 * Handle a collection of plugins
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class PluginManager implements PluginInterface
{
    private $logger, $plugins = array();

    /**
     * @param CompileSettingsInterface $settings
     * @param LoggerInterface $logger
     */
    public function __construct(CompileSettingsInterface $settings, LoggerInterface $logger)
    {
        $this->setLogger($logger);

        $this->registerPlugin(new Core($settings->getRootPath()));

        foreach ($settings->getPlugins() as $plugin) {
            $this->registerPlugin($plugin);
        }
    }

    /**
     * @param  PluginInterface $plugin
     * @return void
     */
    public function registerPlugin(PluginInterface $plugin)
    {
        $this->logger->info("Plugin ".get_class($plugin)." registered");
        $plugin->setLogger($this->logger);
        $this->plugins[] = $plugin;
    }

    /**
     * @param  Definition $definition
     * @return void
     */
    public function processDefinition(Definition $definition)
    {
        foreach ($this->plugins as $plugin) {
            $plugin->processDefinition($definition);
        }
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }
}

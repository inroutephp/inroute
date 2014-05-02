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
use Psr\Log\LoggerInterface;

/**
 * Handle a collection of plugins
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class PluginManager implements PluginInterface
{
    private $plugins = array();

    public function __construct(LoggerInterface $logger)
    {
        $this->setLogger($logger);
    }

    /**
     * @param  PluginInterface $plugin
     * @return void
     */
    public function registerPlugin(PluginInterface $plugin)
    {
        // TODO logg plugin
        // $this->getLogger()->info("Using plugin {get_class($plugin)}");
        $plugin->setLogger($this->getLogger());
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

    public function setLogger(LoggerInterface $logger)
    {
        // TODO use trait instead...
        $this->logger = $logger;
    }

    public function getLogger()
    {
        // TODO use trait instead...
        return $this->logger;
    }
}

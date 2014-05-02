<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Settings;

use inroute\CompileSettingsInterface
use inroute\classtools\ReflectionClassIteratorInterface;
use Psr\Log\LoggerInterface;

/**
 * Merge settings from multiple CompileSettingsInterface objects
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class SettingsManager implements CompileSettingsInterface
{
    private $root = '', $plugins = array();

    public function __construct(ReflectionClassIteratorInterface $settingsClasses, LoggerInterface $logger)
    {
        foreach ($settingsClasses as $reflectedClass) {
            // TODO logg settings class
            //$logger->info("Reading build settings from {$reflectedClass->getName()}");

            $settings = $reflectedClass->newInstance();

            $this->root = $settings->getRootPath();

            $this->plugins = array_merge(
                $this->plugins,
                $settings->getPlugins()
            );
        }
    }

    public function getRootPath()
    {
        return $this->root;
    }

    public function getPlugins()
    {
        return $this->plugins;
    }
}

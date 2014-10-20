<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Settings;

/**
 * Defines compile time settings for inroute project
 *
 * The purpose of the settings interface is the let each inroute build produce
 * the same results, regardless if the build was executed through the command
 * line interface, or in pure php.
 *
 * Implement the SettingsInterface and place the class in the projects directory
 * structure. The inroute compiler will read all implementations found in the
 * defined paths.
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface SettingsInterface
{
    /**
     * Get path to prepend to all routes
     *
     * @return string
     */
    public function getRootPath();

    /**
     * Get array of plugin objects to load att compile time
     *
     * @return \inroute\Plugin\PluginInterface[]
     */
    public function getPlugins();
}

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
 * Wrapper around composer.json files
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ComposerWrapper
{
    /**
     * @var string[] Array of paths
     */
    private $paths = array();

    /**
     * Create instance for composer.json file
     *
     * @param  string $pathToComposerJson
     * @return ComposerWrapper
     */
    public static function createFromFile($pathToComposerJson)
    {
        return new ComposerWrapper(
            (array) json_decode(
                file_get_contents($pathToComposerJson)
            ),
            dirname($pathToComposerJson)
        );
    }

    /**
     * Parse paths from autoload sections of composer data
     *
     * @param array  $composerData
     * @param string $basePath
     */
    public function __construct(array $composerData, $basePath)
    {
        foreach (['autoload', 'autoload-dev'] as $sectionName) {
            if (isset($composerData[$sectionName])) {
                foreach ($composerData[$sectionName] as $section) {
                    foreach ($section as $paths) {
                        foreach ((array)$paths as $path) {
                            $this->paths[$path] = $basePath . DIRECTORY_SEPARATOR. $path;
                        }
                    }
                }
            }
        }
    }

    /**
     * Get parsed paths
     *
     * @return string[]
     */
    public function getPaths()
    {
        return $this->paths;
    }
}

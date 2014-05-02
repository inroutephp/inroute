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
use inroute\classtools\ReflectionClassIterator;
use Psr\Log\LoggerInterface;

/**
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ComposerJsonParser
{
    private $paths = array();

    public function __construct(array $composerData)
    {
        foreach ($composerData['autoload'] as $section) {
            foreach ($section as $path) {
                // TODO absolute path needed!!!!!
                $this->paths[] = $path;
            }
        }
    }

    public function getPaths()
    {
        return $this->paths;
    }
}

<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\classtools;

use ArrayIterator;
use ReflectionClass;
use inroute\Exception\RuntimeException;

/**
 * Iterate over classes found in filesystem
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassIterator implements ReflectionClassIteratorInterface
{
    private $classes = array();

    /**
     * @param array $paths
     */
    public function __construct(array $paths = null)
    {
        foreach ((array)$paths as $path) {
            $this->addPath($path);
        }
    }

    /**
     * @param  string $path
     * @throws RuntimeException If $path is not a valid path
     */
    public function addPath($path)
    {
        if (is_dir($path)) {
            $this->addDir($path);
        } elseif(is_file($path)) {
            $this->addFile($path);
        } else {
            throw new RuntimeException("<$path> is not a valid filesystem path.");
        }
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->classes);
    }

    /**
     * @param  string $dirname
     * @return void
     */
    private function addDir($dirname)
    {
        $this->appendClasses(
            array_keys(
                ClassMapGenerator::createMap($dirname)
            )
        );
    }

    /**
     * @param  string $filename
     * @return void
     */
    private function addFile($filename)
    {
        $this->appendClasses(
            ClassMapGenerator::findClasses($filename)
        );
    }

    /**
     * @param  array $classnames
     * @return void
     */
    private function appendClasses(array $classnames)
    {
        foreach ($classnames as $classname) {
            if (!isset($this->classes[$classname])) {
                $this->classes[$classname] = new ReflectionClass($classname);
            }
        }
    }
}

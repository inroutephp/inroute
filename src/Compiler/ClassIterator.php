<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use IteratorAggregate;
use ArrayIterator;
use inroute\Exception\RuntimeException;

/**
 * Iterate over classes found in filesystem
 *
 * ClassIterator is a facade to ClassMapGenerator
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassIterator implements IteratorAggregate
{
    private $classes = array();

    /**
     * @param  array $paths
     * @throws RuntimeException If a nonvalid path is supplied
     */
    public function __construct(array $paths = null)
    {
        foreach ((array)$paths as $path) {
            if (is_dir($path)) {
                $this->addDir($path);
            } elseif(is_file($path)) {
                $this->addFile($path);
            } else {
                throw new RuntimeException("<$path> is not a valid filesystem path.");
            }
        }
    }

    /**
     * @param  string $dirname
     * @return void
     */
    public function addDir($dirname)
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
    public function addFile($filename)
    {
        $this->appendClasses(
            ClassMapGenerator::findClasses($filename)
        );
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        return new ArrayIterator($this->classes);
    }

    /**
     * @param  array $classes
     * @return void
     */
    private function appendClasses(array $classes)
    {
        $this->classes = array_unique(
            array_merge(
                $this->classes,
                $classes
            )
        );
    }
}

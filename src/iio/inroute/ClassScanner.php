<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace iio\inroute;

/**
 * Scan filesystem for classes
 *
 * ClassScanner is actually a facade to ClassMapGenerator to
 * enable dependancy injection.
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassScanner
{
    /**
     * @var array List of found classes
     */
    private $classes = array();

    /**
     * Append classes to store
     *
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

    /**
     * Add directory to scan
     * 
     * @param  string       $dirname
     * @return ClassScanner Instance for chaining
     */
    public function addDir($dirname)
    {
        $this->appendClasses(
            array_keys(
                ClassMapGenerator::createMap($dirname)
            )
        );

        return $this;
    }

    /**
     * Scan file and process found classes
     *
     * @param  string       $filename
     * @return ClassScanner Instance for chaining
     */
    public function addFile($filename)
    {
        $this->appendClasses(
            ClassMapGenerator::findClasses($filename)
        );

        return $this;
    }

    /**
     * Get list of found classes
     *
     * @return array
     */
    public function getClasses()
    {
        return $this->classes;
    }
}

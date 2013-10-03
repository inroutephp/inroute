<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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
     * Add directory to scan
     * 
     * @param  string       $dirname
     * @return ClassScanner Instance for chaining
     */
    public function addDir($dirname)
    {
        $map = ClassMapGenerator::createMap($dirname);
        $this->classes = array_merge(
            $this->classes,
            array_keys($map)
        );

        return $this;
    }

    /**
     * Scan file and process found classes
     *
     * @param  string           $filename
     * @return ClassScanner     Instance for chaining
     */
    public function addFile($filename)
    {
        $this->classes = array_merge(
            $this->classes,
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

<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace itbz\inroute;

use itbz\inroute\Exception\RuntimeExpection;
use Symfony\Component\Finder\Finder;
use LogicException;

/**
 * Scan filesystem for classes
 * 
 * @package inroute
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 */
class ClassScanner
{
    /**
     * Finder object
     *
     * @var Finder
     */
    private $finder;

    /**
     * List of found classes
     *
     * @var array
     */
    private $classes = array();

    /**
     * Scan filesystem for classes
     *
     * @param Finder $finder
     */
    public function __construct(Finder $finder)
    {
        $finder->files();
        $this->finder = $finder;
    }

    /**
     * Add file prefix to scan for
     *
     * Prefixes are only used togheter with directories
     * 
     * @param string $prefix
     *
     * @return ClassScanner instance for chaining
     */
    public function addPrefix($prefix)
    {
        $this->finder->name("*.$prefix");

        return $this;
    }

    /**
     * Add file directory to scan for
     * 
     * @param string $dirname
     *
     * @return ClassScanner instance for chaining
     */
    public function addDir($dirname)
    {
        $this->finder->in($dirname);

        return $this;
    }

    /**
     * Perform scan and get list of class names
     *
     * @return array List of classes
     */
    public function getClasses()
    {
        try {
            foreach ($this->finder as $file) {
                $this->addFile($file->getRealpath());
            }
        } catch (LogicException $e) {
            // LogicException is thrown when no directory is appended to
            // finder. We can ignore this.
        }

        return $this->classes;
    }

    /**
     * Scan file and process found classes
     *
     * @param string $filename
     *
     * @return ClassScanner instance for chaining
     *
     * @throws RuntimeException If $filename is not readable
     * @throws RuntimeException If $filename is already included
     */
    public function addFile($filename)
    {
        if (!is_file($filename) or !is_readable($filename)) {
            $msg = "$filename is not a readable file";
            throw new RuntimeExpection($msg);
        }

        if (in_array(realpath($filename), get_included_files())) {
            $msg = "$filename can not be scanned: already included.";
            throw new RuntimeExpection($msg);
        }

        $currentClasses = get_declared_classes();
        include_once $filename;

        $this->classes = array_merge(
            $this->classes,
            array_diff(get_declared_classes(), $currentClasses)
        );

        return $this;
    }
}

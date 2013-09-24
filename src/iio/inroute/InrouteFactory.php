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

use Mustache_Engine;
use Mustache_Loader_ArrayLoader;
use Symfony\Component\Finder\Finder;

/**
 * User access class for the inroute package
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class InrouteFactory
{
    /**
     * @var array Inroute settings
     */
    private $settings = array(
        "root" => "",
        "prefixes" => array("php"),
        "dirs" => array('.'),
        "files" => array(),
        "classes" => array()
    );

    /**
     * @var ClassScanner Class scanner object
     */
    private $scanner;

    /**
     * @var CodeGenerator Code generator
     */
    private $generator;

    /**
     * Constructor
     *
     * InrouteFactory works both in an injectionist and a standalone way. You
     * may inject a mustache enginge and a ClassScanner, if not they will be
     * created for you.
     *
     * @param CodeGenerator $generator
     * @param ClassScanner  $scanner
     */
    public function __construct(
        CodeGenerator $generator = null,
        ClassScanner $scanner = null
    ) {
        if (!$generator) {
            $templatedir = __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;
            $loader = new Mustache_Loader_ArrayLoader(
                array(
                    'Dependencies' => file_get_contents($templatedir  . 'Dependencies.mustache'),
                    'routes' => file_get_contents($templatedir  . 'routes.mustache'),
                    'static' => file_get_contents($templatedir  . 'static.mustache')
                )
            );
            $mustache = new Mustache_Engine(array('loader' => $loader));
            $generator = new CodeGenerator($mustache);
        }
        $this->generator = $generator;

        if (!$scanner) {
            $scanner = new ClassScanner(new Finder);
        }
        $this->scanner = $scanner;
    }

    /**
     * Generate code that returns on inroute instance
     *
     * @return string
     */
    public function generate()
    {
        foreach ((array) $this->settings['prefixes'] as $prefix) {
            $this->scanner->addPrefix($prefix);
        }
        foreach ((array) $this->settings['dirs'] as $dirname) {
            $this->scanner->addDir($dirname);
        }
        foreach ((array) $this->settings['files'] as $filename) {
            $this->scanner->addFile($filename);
        }

        return $this->generator
            ->addClasses($this->scanner->getClasses())
            ->addClasses((array)$this->settings['classes'])
            ->setRoot($this->settings['root'])
            ->generate();
    }

    /**
     * Set prefixes to search for
     *
     * @param  string|array   $prefixes
     * @return InrouteFactory Instance for chaining
     */
    public function setPrefixes($prefixes)
    {
        assert('is_string($prefixes) || is_array($prefixes)');
        $this->settings['prefixes'] = $prefixes;

        return $this;
    }

    /**
     * Set directories to scan for classes
     *
     * @param  string|array   $dirs
     * @return InrouteFactory Instance for chaining
     */
    public function setDirs($dirs)
    {
        assert('is_string($dirs) || is_array($dirs)');
        $this->settings['dirs'] = $dirs;

        return $this;
    }

    /**
     * Set files to scan for classes
     *
     * @param  string|array   $files
     * @return InrouteFactory Instance for chaining
     */
    public function setFiles($files)
    {
        assert('is_string($files) || is_array($files)');
        $this->settings['files'] = $files;

        return $this;
    }

    /**
     * Set classes to include
     *
     * @param  string|array   $classes
     * @return InrouteFactory Instance for chaining
     */
    public function setClasses($classes)
    {
        assert('is_string($classes) || is_array($classes)');
        $this->settings['classes'] = $classes;

        return $this;
    }

    /**
     * Set project www root
     *
     * @param  string         $root
     * @return InrouteFactory Instance for chaining
     */
    public function setRoot($root)
    {
        assert('is_string($root)');
        $this->settings['root'] = $root;

        return $this;
    }

    /**
     * Get current settings. For testing.
     * 
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }
}

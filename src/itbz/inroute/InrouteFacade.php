<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package itbz\inroute
 */

namespace itbz\inroute;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Symfony\Component\Finder\Finder;

/**
 * User access class for the inroute package
 *
 * @package itbz\inroute
 */
class InrouteFacade
{
    /**
     * Inroute settings
     *
     * @var array
     */
    private $settings = array(
        "root" => "",
        "caller" => "DefaultCaller",
        "container" => "",
        "prefixes" => array("php"),
        "dirs" => "",
        "files" => ""
    );

    /**
     * Class scanner object
     *
     * @var ClassScanner
     */
    private $scanner;

    /**
     * Router generator
     *
     * @var RouterGenerator
     */
    private $generator;

    /**
     * User access class for the inroute package
     *
     * InrouteFacade works both in an injectionist and a standalone way. You
     * may inject a mustache enginge and a ClassScanner, if not facade will try
     * to create it for you.
     *
     * @param RouterGenerator $generator
     * @param ClassScanner $scanner
     */
    public function __construct(
        RouterGenerator $generator = null,
        ClassScanner $scanner = null
    ) {
        if (!$generator) {
            $templatedir = __DIR__ . DIRECTORY_SEPARATOR . 'Templates';
            $mustache = new Mustache_Engine(
                array(
                    'loader' => new Mustache_Loader_FilesystemLoader($templatedir)
                )
            );
            $generator = new RouterGenerator($mustache);
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

        foreach ($this->scanner->getClasses() as $classname) {
            $this->generator->addClass($classname);
        }

        return $this->generator->setRoot($this->settings['root'])
            ->setCaller($this->settings['caller'])
            ->setContainer($this->settings['container'])
            ->generate();
    }

    /**
     * Set prefixes to search for
     *
     * @param string $prefixes
     *
     * @return InrouteFacade instance for chaining
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
     * @param string $dirs
     *
     * @return InrouteFacade instance for chaining
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
     * @param string $files
     *
     * @return InrouteFacade instance for chaining
     */
    public function setFiles($files)
    {
        assert('is_string($files) || is_array($files)');
        $this->settings['files'] = $files;

        return $this;
    }

    /**
     * Set project www root
     *
     * @param string $root
     *
     * @return InrouteFacade instance for chaining
     */
    public function setRoot($root)
    {
        assert('is_string($root)');
        $this->settings['root'] = $root;

        return $this;
    }

    /**
     * Set caller classname
     *
     * @param string $caller
     *
     * @return InrouteFacade instance for chaining
     */
    public function setCaller($caller)
    {
        assert('is_string($caller)');
        $this->settings['caller'] = $caller;

        return $this;
    }

    /**
     * Set container classname
     *
     * @param string $container
     *
     * @return InrouteFacade instance for chaining
     */
    public function setContainer($container)
    {
        assert('is_string($container)');
        $this->settings['container'] = $container;

        return $this;
    }

    /**
     * Load settings from array
     *
     * @param array $settings
     *
     * @return InrouteFacade instance for chaining
     */
    public function loadSettings(array $settings)
    {
        $this->settings = array_merge($this->settings, $settings);

        return $this;
    }

    /**
     * Get current settings
     *
     * For testing
     * 
     * @return array
     */
    public function getSettings()
    {
        return $this->settings;
    }
}

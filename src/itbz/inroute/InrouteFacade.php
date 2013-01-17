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
     * User access class for the inroute package
     *
     * @param string $filename Name of json settings file
     */
    public function __construct($filename)
    {
        if ($filename) {
            $this->loadSettings((array)json_decode(file_get_contents($filename)));
        }
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
     * Generate code that returns on inroute instance
     *
     * @return string
     */
    public function generate()
    {
        $templatedir = __DIR__ . DIRECTORY_SEPARATOR . 'Templates';
        $mustache = new Mustache_Engine(
            array(
                'loader' => new Mustache_Loader_FilesystemLoader($templatedir)
            )
        );

        $scanner = new ClassScanner(new Finder);

        foreach ((array) $this->settings['prefixes'] as $prefix) {
            $scanner->addPrefix($prefix);
        }

        foreach ((array) $this->settings['dirs'] as $dirname) {
            $scanner->addDir($dirname);
        }

        foreach ((array) $this->settings['files'] as $filename) {
            $scanner->addFile($filename);
        }

        $generator = new RouterGenerator($mustache, $scanner);

        return $generator->setRoot($this->settings['root'])
            ->setCaller($this->settings['caller'])
            ->setContainer($this->settings['container'])
            ->generate();
    }
}

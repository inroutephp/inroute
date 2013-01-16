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
     * Name of template directory
     *
     * @var string
     */
    private $templatedir;

    /**
     * File prefixes to scan for
     *
     * @var array|string
     */
    private $prefixes;

    /**
     * Directories to scan for classes
     *
     * @var array|string
     */
    private $dirs;

    /**
     * Files to scan for classes
     *
     * @var array|string
     */
    private $files;

    /**
     * Project www root
     *
     * @var string
     */
    private $root;

    /**
     * Caller classname
     *
     * @var string
     */
    private $caller = 'DefaultCaller';

    /**
     * Container classname
     *
     * @var string
     */
    private $container;

    /**
     * User access class for the inroute package
     *
     * @param array $settings
     */
    public function __construct(array $settings = null)
    {
        if ($settings) {
            $this->loadSettings($settings);
        }
    }

    /**
     * Set name of template directory
     *
     * @param string $templatedir
     *
     * @return InrouteFacade instance for chaining
     */
    public function setTemplateDir($templatedir)
    {
        $this->templatedir = $templatedir;

        return $this;
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
        $this->prefixes = $prefixes;

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
        $this->dirs = $dirs;

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
        $this->files = $files;

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
        $this->root = $root;

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
        $this->caller = $caller;

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
        $this->container = $container;

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
        if (isset($settings['root'])) {
            $this->setRoot($settings['root']);
        }
        if (isset($settings['caller'])) {
            $this->setCaller($settings['caller']);
        }
        if (isset($settings['container'])) {
            $this->setContainer($settings['container']);
        }
        if (isset($settings['prefixes'])) {
            $this->setPrefixes($settings['prefixes']);
        }
        if (isset($settings['dirs'])) {
            $this->setDirs($settings['dirs']);
        }
        if (isset($settings['files'])) {
            $this->setFiles($settings['files']);
        }
        if (isset($settings['templatedir'])) {
            $this->setTemplatedir($settings['templatedir']);
        }

        return $this;
    }

    /**
     * Generate code that returns on inroute instance
     *
     * @return string
     */
    public function generate()
    {
        $mustache = new Mustache_Engine(
            array(
                'loader' => new Mustache_Loader_FilesystemLoader($this->templatedir)
            )
        );

        $scanner = new ClassScanner(new Finder);

        foreach ((array) $this->prefixes as $prefix) {
            $scanner->addPrefix($prefix);
        }

        foreach ((array) $this->dirs as $dirname) {
            $scanner->addDir($dirname);
        }

        foreach ((array) $this->files as $filename) {
            $scanner->addFile($filename);
        }

        $generator = new RouterGenerator($mustache, $scanner);

        return $generator->setRoot($this->root)
            ->setCaller($this->caller)
            ->setContainer($this->container)
            ->generate();
    }
}

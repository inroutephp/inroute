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

/**
 * User access class for the inroute package
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class InrouteFactory
{
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
    public function __construct(CodeGenerator $generator = null, ClassScanner $scanner = null)
    {
        if (!$generator) {
            $tmpldir = __DIR__ . DIRECTORY_SEPARATOR . 'Templates' . DIRECTORY_SEPARATOR;
            $loader = new Mustache_Loader_ArrayLoader(
                array(
                    'Dependencies' => file_get_contents($tmpldir . 'Dependencies.mustache'),
                    'routes'       => file_get_contents($tmpldir . 'routes.mustache'),
                    'static'       => file_get_contents($tmpldir . 'static.mustache')
                )
            );
            $mustache = new Mustache_Engine(array('loader' => $loader));
            $generator = new CodeGenerator($mustache);
        }

        if (!$scanner) {
            $scanner = new ClassScanner();
        }

        $this->generator = $generator;
        $this->scanner = $scanner;
    }

    /**
     * Add source directories
     *
     * @param  array $dirs
     * @return void
     */
    public function addDirs(array $dirs)
    {
        foreach ($dirs as $dir) {
            $this->scanner->addDir($dir);
        }
    }

    /**
     * Add source file names
     *
     * @param  array $filenames
     * @return void
     */
    public function addFiles(array $filenames)
    {
        foreach ($filenames as $filename) {
            $this->scanner->addFile($filename);
        }
    }

    /**
     * Add source class names
     *
     * @param  array $classnames
     * @return void
     */
    public function addClasses(array $classnames)
    {
        $this->generator->addClasses($classnames);
    }

    /**
     * Set project www root
     *
     * @param  string $root
     * @return void
     */
    public function setRoot($root)
    {
        assert('is_string($root)');
        $this->generator->setRoot($root);
    }

    /**
     * Generate code that returns an inroute instance
     *
     * @return string
     */
    public function generate()
    {
        $this->generator->addClasses($this->scanner->getClasses());

        return $this->generator->generate();
    }
}

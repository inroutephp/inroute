<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerAwareInterface;
use Psr\Log\NullLogger;
use inroute\Compiler\Compiler;
use inroute\Settings\ComposerWrapper;
use hanneskod\classtools\ClassIterator;
use hanneskod\classtools\FilterableClassIterator;

/**
 * Facade to the compiler package
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class InrouteFactory implements LoggerAwareInterface
{
    /**
     * @var ClassIterator Project class iterator
     */
    private $classIterator;

    /**
     * @var LoggerInterface Compilation event logger
     */
    private $logger;

    /**
     * Optionally inject class iterator
     *
     * @param ClassIterator $classIterator
     */
    public function __construct(ClassIterator $classIterator = null)
    {
        $this->classIterator = $classIterator ?: new ClassIterator;
    }

    /**
     * Add paths read from composer.json
     *
     * @param  string $pathToComposerJson
     * @return void
     */
    public function parseComposerJson($pathToComposerJson)
    {
        if (!is_readable($pathToComposerJson)) {
            $this->getLogger()->warning("Unable to parse composer settings from <$pathToComposerJson>");
            return;
        }

        $this->getLogger()->info("Reading paths from <$pathToComposerJson>");

        foreach (ComposerWrapper::createFromFile($pathToComposerJson)->getPaths() as $path) {
            $this->addPath($path);
        }
    }

    /**
     * Add path to class iterator
     *
     * @param  string $path
     * @return void
     */
    public function addPath($path)
    {
        try {
            $this->classIterator->addPath($path);
            $this->getLogger()->info("Using path <$path>");
        } catch (\hanneskod\classtools\Exception\RuntimeException $e) {
            $this->getLogger()->error($e->getMessage());
        }
    }

    /**
     * Create compiler for this build
     *
     * @return Compiler
     */
    public function createCompiler()
    {
        return new Compiler(
            new FilterableClassIterator($this->classIterator),
            $this->getLogger()
        );
    }

    /**
     * Compile project
     *
     * @return string
     */
    public function generate()
    {
        return $this->createCompiler()->compile();
    }

    /**
     * Set compile event logger
     *
     * @param  LoggerInterface $logger [description]
     * @return void
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * Get compile event logger
     *
     * @return LoggerInterface
     */
    public function getLogger()
    {
        if (!isset($this->logger)) {
            $this->logger = new NullLogger;
        }
        return $this->logger;
    }
}

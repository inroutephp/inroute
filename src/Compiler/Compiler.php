<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use hanneskod\classtools\FilterableClassIterator;
use hanneskod\classtools\ClassIterator;
use hanneskod\classtools\Instantiator;
use Psr\Log\LoggerInterface;
use inroute\Plugin\PluginManager;
use inroute\Settings\SettingsManager;

/**
 * Compile inroute project
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Compiler
{
    /**
     * @var FilterableClassIterator Project iterator
     */
    private $classIterator;

    /**
     * @var LoggerInterface Compile event logger
     */
    private $logger;

    /**
     * Constructor
     *
     * @param FilterableClassIterator $classIterator
     * @param LoggerInterface         $logger
     */
    public function __construct(FilterableClassIterator $classIterator, LoggerInterface $logger)
    {
        $this->classIterator = $classIterator;
        $this->logger = $logger;
    }

    /**
     * Generate php code from project
     *
     * @return string
     */
    public function compile()
    {
        return (string) new CodeGenerator(
            new RouteFactory(
                new DefinitionFactory(
                    $this->classIterator,
                    new PluginManager(
                        new SettingsManager(
                            $this->classIterator,
                            $this->logger,
                            new Instantiator
                        ),
                        $this->logger
                    ),
                    $this->logger
                ),
                new PathTokenizer
            ),
            new FilterableClassIterator(
                new ClassIterator([__DIR__.'/../Router'])
            )
        );
    }
}

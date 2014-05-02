<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use inroute\classtools\ReflectionClassIterator;
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
    private $classIterator, $logger;

    public function __construct(ReflectionClassIterator $classIterator, LoggerInterface $logger)
    {
        $this->classIterator = $classIterator;
        $this->logger = $logger;
    }

    public function compile()
    {
        return (string) new CodeGenerator(
            new RouteFactory(
                new DefinitionFactory(
                    $this->classIterator,
                    new PluginManager(
                        new SettingsManager(
                            $this->classIterator,
                            $this->logger
                        ),
                        $this->logger
                    ),
                    $this->logger
                )
            ),
            new ReflectionClassIterator(
                array(
                    __DIR__.'/../Router',
                    __DIR__.'/../ControllerInterface.php'
                )
            )
        );
    }
}

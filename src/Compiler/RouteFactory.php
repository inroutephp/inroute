<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use inroute\PluginInterface;
use inroute\Router\Route;
use Closure;

/**
 * Create route objects from reflected controller classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RouteFactory
{
    private $caller, $tokenizer, $plugins = array(), $routes = array();

    /**
     * @param Closure   $caller    Closure used when invoking routes
     * @param Tokenizer $tokenizer Tokenizer used when parsing paths
     */
    public function __construct(Closure $caller, Tokenizer $tokenizer = null)
    {
        $this->caller = $caller;
        $this->tokenizer = $tokenizer ?: new Tokenizer;
    }

    /**
     * Load inroute plugin
     *
     * @param  PluginInterface $plugin
     * @return void
     */
    public function loadPlugin(PluginInterface $plugin)
    {
        $this->plugins[] = $plugin;
    }

    /**
     * Create routes from route descriptions
     *
     * @param  DefinitionFinderOld $definitions
     * @return void
     */
    public function addRoutes(DefinitionFinderOld $definitions)
    {
        foreach ($definitions as $def) {
            $this->routes[] = new Route(
                $this->tokenizer->tokenize($def['path']),
                $this->tokenizer->getRegex(),
                $def['httpmethods'],
                $def['controller'],
                $def['controllerMethod'],
                $this->caller
            );
        }
    }

    /**
     * Get routes definied in controller
     *
     * @return array<Route>
     */
    public function getRoutes()
    {
        return $this->routes;
    }

    /*
        // Route->invoke(): bättre om den tar Caller som argument
            det är galet att varje route ska lagra referens till samma caller-object!!!

        // RouteFactory ska ta ett DefinitionFactory-object till konstrukt
            // ska implementera IteratorAggregate
            // kan gärna returnera en generator från getIterator..

        // För att kompilera har vi nu:
            $code = (string)new CodeGenerator(
                new RouteFactory(
                    new DefinitionFactory(
                        new ClassIterator(
                            array(
                                $path1,
                                $path2
                            )
                        ),
                        new PluginManager(
                            new $plugin1,
                            new $plugin2
                        )
                    ),
                    $caller
                )
            );
        // Denna kod kan wrappas i en Compiler med ett förenklat gränssnitt...
        // eller varför inte helt enkelt låta detta var i BuildCommand
            $code = (
                new Compiler(
                    array(
                        $path1,
                        $path2
                    ),
                    array(
                        new $plugin1,
                        new $plugin2
                    ),
                    $caller
                )
            )->compile();
        // och så när swagger kommer så skriver jag ett SwaggerCommand
            $ php bin/inroute swagger ...

        Anpassa exempel mm till den nya annotations-syntaxen
            * https://github.com/pgraham/php-annotations

        Det som är tests/data kan kanske flytta till example istället??
            det hade varit tjusigare om dessa klasser spelade en dubbel roll
            och då skulle vi få automatiska tester av att example verkligen
            fungerar

        Todo: om multiple @route tags i controller ska leda till olika definitions så måste jag arbeta om 
            på något sätt..
     */
}

<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use IteratorAggregate;

/**
 * Create Routes from Definition
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class RouteFactory implements IteratorAggregate
{
    private $definitions, $tokenizer, $routes = array();

    /**
     * @param DefinitionFactory $definitions Route definition source
     * @param Tokenizer         $tokenizer   Tokenizer used when parsing paths
     */
    public function __construct(DefinitionFactory $definitions, Tokenizer $tokenizer = null)
    {
        $this->definitions = $definitions;
        $this->tokenizer = $tokenizer ?: new Tokenizer;
    }

    /**
     * @return \Iterator
     * @todo   Implement as a generator
     */
    public function getIterator()
    {
        $routes = array();

        foreach ($this->definitions as $definition) {
            $routes[] = new Route(
                $this->tokenizer->tokenize($definition->path),
                $this->tokenizer->getRegex(),
                $definition->httpmethods,
                $definition->controller,
                $definition->controllerMethod,
                $definition->getPreFilters(),
                $definition->getPostFilters()
            );
        }

        return \ArrayIterator($routes);
    }

    /*
        Skriv test för RouteFatory!!

        // Dokumentera hur pre och post filter ska vara utformade...
            Kolla i Route och RouteTest hur jag har skrivit
            Skriv dokumentationen till PluginInterface
                Även om CompilerSkipRouteException
            Det är ju viktigt att utvecklare vet hur de ska skriva sina filter..

            Däremot så kan filter behöva känna till data från Definition
                @accept text/plain => då måste ju filter veta vad det stod i
                annotation
            Detta kan sparas i Closure som laddas i Plugin
                spara kommentarer om detta i Core så länge...
            Data läggs till genom att ändra på args i closure..

        //I DefinitionFactoryTest kan jag kanske använda något annat än __CLASS__
            det skulle göra att jag kan köra test snabbare..

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
                    )
                )
            );

        Anpassa exempel mm till den nya annotations-syntaxen
            * https://github.com/pgraham/php-annotations

        Det som är tests/data kan kanske flytta till example istället??
            det hade varit tjusigare om dessa klasser spelade en dubbel roll
            och då skulle vi få automatiska tester av att example verkligen
            fungerar
     */
}

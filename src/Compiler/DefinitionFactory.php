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
use ReflectionClass;
use inroute\PluginInterface;

/**
 * Create route definitions from controller classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionFactory implements IteratorAggregate
{
    private $classes, $plugin;

    /**
     * @param ClassIterator   $classes
     * @param PluginInterface $plugin
     */
    public function __construct(ClassIterator $classes, PluginInterface $plugin)
    {
        $this->classes = $classes;
        $this->plugin = $plugin;
    }

    /**
     * @return \Iterator
     * @todo   Implement as a generator
     */
    public function getIterator()
    {
        $definitions = array();

        foreach ($this->classes as $className) {
            /** @var Definition $definition */
            foreach (new DefinitionIterator(new ReflectionClass($className)) as $definition) {
                try {
                    $this->plugin->processDefinition($definition);
                    $definitions[] = $definition;
                } catch (CompilerSkipException $e) {
                }
            }
        }

        return new \ArrayIterator($definitions);
    }

    /*
        Nästa steg nu är att skriva test till DefinitionFactory
            sen kan snart DefinitionFinderOld samt Tag/ försvinna...

        // Core kan läsa definition på detta sätt...
        $compiler = new Compiler;
        $compiler->loadPlugin(new Plugin\Core);
            Men hur ska vi i så fall fatta att en klass/metod inte ska routas??
                throw new CompilerSkipException()

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

        Skriv om Exceptions så att den arbetar med SPL exceptions!!

        Todo: om multiple @route tags i controller ska leda till olika definitions så måste jag arbeta om 
            på något sätt..
     */
}

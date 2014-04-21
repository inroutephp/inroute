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
 * Create route definitions from parsed classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DefinitionFactory implements IteratorAggregate
{
    private $classes, $pluginManager;

    /**
     * @param ClassFinder     $classes
     * @param PluginInterface $pluginManager
     */
    public function __construct(ClassFinder $classes, PluginInterface $pluginManager = null)
    {
        $this->classes = $classes;
        $this->pluginManager = $pluginManager ?: new PluginManager;
    }

    /**
     * @return \Iterator
     */
    public function getIterator()
    {
        // Bra läge att implementera som en generator!!
        $definitions = array();

        foreach ($this->classes as $class) {
            foreach (new DefinitionFinder($class) as $def) {
                try {
                    $definitions[] = $this->pluginManager->processDefinition($def);
                    // Plugin ska registrera till def den Closure som ska köras
                    // pre eller post vid routing...
                    // detta ska sparas i Definition->preFilters samt postFilters
                    // och kan utelämnas i Definition->toArray()
                } catch (CompilerSkipException $e) {
                }
            }
        }

        return new ArrayIterator($definitions);
    }

    /*
        // Definition är programmatisk representation av annotations...
        $def = new Definition($classAnnotations, $methodAnnotations)
            ->getAnnotations() ->getAnnotationByName() osv...
            ->set($key, $value) //error om $key redan är satt... //används i plugin->processDefinition
            ->toArray() //hämta definition som array

        // Plugins kan arbeta på def bäst de vill...
        new Plugin()->processDefinition($def)
        
        // Core kan läsa definition på detta sätt...
        $compiler = new Compiler;
        $compiler->loadPlugin(new Plugin\Core);
            Det är bra därför att jag samlar kod för att ändra syntax på ett ställe!
            Men hur ska vi i så fall fatta att en klass/metod inte ska routas??
                throw new CompilerSkipException()
            Hur ska det i så fall fungera mrf plugins i RouteFactory
                först måste Route skapas
            Lägg till möjlighet att sätta en root-path till Core
                så att denna path prependas till alla paths som skapas
                se CodeGenerator i gamla versionen...

        // DefinitionFinder skapar en Definition per metod i klass
        DefinitionFinder($class)
            // väldigt enkel wrapper till annotations-lib.
            // Bara hämtar annotations och skapar Definitions
            // Bra användningsområde för en generator??

        // Använd något externt bibliotek för att läsa annotations
            * https://github.com/pgraham/php-annotations
                testa med denna! det är antagligen det enklaste
                kräver att jag ändrar syntaxen något..

            * https://github.com/marcioAlmada/annotations
                Har stöd för namespaced annotations
                    @route.access ...
                php 5.4

        // PluginManager implements PluginInterface
            // hanterar massa olika plugins på en gång
            // samt ser till att Core alltid laddas

        // RouteFactory ska ta ett DefinitionFactory-object till konstrukt
            // ska implementera IteratorAggregate
            // kan gärna returnera en generator från getIterator..

        // För att kompilera har vi nu:
            $code = (string)new CodeGenerator(
                new RouteFactory(
                    new DefinitionFactory(
                        new ClassFinder(
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


        Det som är tests/data kan kanske flytta till example istället??
            det hade varit tjusigare om dessa klasser spelade en dubbel roll
            och då skulle vi få automatiska tester av att example verkligen
            fungerar
     */
}

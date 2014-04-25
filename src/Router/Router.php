<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Router;

/**
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Router
{
    private $routes;

    /**
     * @param Route[] $routes
     */
    public function __construct(array $routes)
    {
        $this->routes = $routes;
    }

    /*
        Skriv Router
            (tänk efter vilka funktioner som jag tycker borde finnas)
            ->reverse routing efter namn
                (kanske möjliget att namnge när router skapas)
                    så att en kan använda lite finare namn
                    det autogenererade namnet behöver kanske bara användas om inget annat finns
            ->getRoute($name)
                hämtar route efter namn
            ->generatePath($routeName, array $params)
                generera en path för en viss namngiven route
            ->resolve($path, $httpMethd)
                returnerar ett Route object
            ->dispatch($path, $httpMethod, $caller)
                kör hela baletten
            -> ... fler??

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
                            array(
                                new $plugin1,
                                new $plugin2
                            )
                        )
                    )
                )
            );
            // Skriv en Compiler med föränklat gränssnitt
                // att användas vid development bootstraping (se example/development.php)

        Anpassa exempel mm till den nya annotations-syntaxen
            * https://github.com/pgraham/php-annotations
            * Core måste läsa rätt från annotation, skriv klart Core samtidigt...

        ExampleIntegrationTest
            Bygger på /example och kontrollerar att allt routas som det ska
            Använda så mycket som möjligt av genererad router.php, för code-coverage
            Router paketet får inte använda use då class minimizer inte stödjer det...
            Kontroll av att un/serialization av routes fungerar

        //  ** Accept plugin skulle skriva något sånt här **
            // skriv en sådan här plugin till example
                // se till att example blir ett live example på både hur filter kan ändra i $args
                // samt hur de kan andra i $return
            // Ha även med CompilerSkipRouteException samt NextRouteException i exemplet
            $definition->write(
                'content-type',
                $definition->getMethodAnnotation('content-type')
            );
            $definition->addPreFilter(function(array &$args) use ($definition) {
                $request = $args['httpRequest'];                    // Finns pga annan plugin..
                $userAcceptContent = $request->getContentType();    // Eller hur nu request fungerar..
                if ($userAcceptContent != $definition->read('content-type')) {
                    throw new NextRouteException;
                }
            });
            // Kolla i Route och RouteTest hur jag har skrivit
            // Byt namn på klasserna bäst jag vill
                Kanske behöver jag ändra i ClassIteratorTest efteråt för att det ska bli rätt

        // Flytta ClassIterator och ClassMinimizer till hanneskod/classtools
        // Om ClassMinimizer ska kunna se vilka use statements som hör
            till vilken klass så måste hela syntax tree analyseras
            https://github.com/nikic/PHP-Parser

            Med hjälp av detta kan alla use statements översättas till originalnamnet
                (tar bort risken för kollisioner)

            Sedan kan jag lägga till ClassMinimizer->getNamespacedPhpCode()

            Implementera som filter??
                echo new Minimizer(new Namespacer(new ClassExtractor(...)));
     */
}

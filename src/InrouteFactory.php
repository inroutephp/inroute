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
use inroute\Settings\ComposerJsonParser;
use hanneskod\classtools\ClassIterator;
use hanneskod\classtools\FilterableClassIterator;

/**
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class InrouteFactory implements LoggerAwareInterface
{
    private $classIterator, $logger;

    public function parseComposerJson($pathToComposerJson)
    {
        $this->getLogger()->info("Reading paths from $pathToComposerJson");

        $parser = new ComposerJsonParser(
            json_decode(
                file_get_contents($pathToComposerJson)
            )
        );

        $this->classIterator = new ClassIterator($parser->getPaths());
    }

    public function getClassIterator()
    {
        if (!isset($this->classIterator)) {
            $this->classIterator = new ClassIterator;
        }
        return $this->classIterator;
    }

    public function addPath($path)
    {
        $this->getLogger()->info("Using path $path");
        $this->getClassIterator()->addPath($path);
    }

    public function getCompiler()
    {
        return new Compiler(
            new FilterableClassIterator($this->getClassIterator()),
            $this->getLogger()
        );
    }

    public function generate()
    {
        return $this->getCompiler()->compile();
    }

    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function getLogger()
    {
        if (!isset($this->logger)) {
            $this->logger = new NullLogger;
        }
        return $this->logger;
    }
}

    /*
        Scrutinizers version av php-analyzer klarar inte av yield
            är det någonting som jag kan göra någonting åt
            eller måste jag bara vänta på att scrutinizer uppdaterar sin kod??

        Skriv ComposerJsonWrapper
            läs paths från autoload
            behöver antagligen ta path istället för array
            skriv det testbart...

        //  ** Accept plugin skulle skriva något sånt här **
            // #### FEL FEL FEL jag kan aldrig!! referera till Definition i plugin!!  ####
                Definition finns ej tillgängligt när router.php körs.
                Jag måste hitta på något annat sätt att spara data som ska skickas med till
                array $args till plugin!!!!
                Kanske ska allt som skrivs till Definition flyttas över till $args???
                    Det blir det enklaste och antagligen kraftfullaste sättet att göra det på...
            // skriv en sådan här plugin till example
                example ett example på hur filter kan ändra i $args samt i $return
                Ha med CompilerSkipRouteException och NextRouteException
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
            Kolla i Route och RouteTest hur jag har skrivit

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

        Anpassa exempel mm till den nya annotations-syntaxen
            * https://github.com/pgraham/php-annotations
            * Core måste läsa rätt från annotation, skriv klart Core samtidigt...
            * Example ska göra allt kul som inroute kan...
                (Och presentera det med trevlig html...)

        ExampleIntegrationTest
            Bygger på /example och kontrollerar att allt routas som det ska
            Använda så mycket som möjligt av genererad router.php, för code-coverage
            Är testet som validerar kompileringsrutinen...
            Router paketet får inte använda use då class minimizer inte stödjer det...
            Kontroll av att un/serialization av routes fungerar
            @runInSeparateProcess
                jag kan skapa en fil där Compiletime-klasserna definieras, men undantag kastas
                    om de instantieras
                på så sätt får jag error om klasser som inte ska användas i Runtime används i IntegrationTest
                det kräver runInSeparateProcess så att denna include bara görs för integration test
            setup(){$this->router = eval(Compiler::compile('inroute example app'))}
                //ska göras en gång när testet startas
                //sen ska denna router användas på alla tänkbara sätt

        // Kolla hur loggandet fungerar när jag skriver Example...
            vilken composer.json den läser (DONE InrouteFactory)
            vilken fil den skriver till (DONE Comment in Command)
            vilka controllers den hittar (DONE DefinitionFactory)
            vilka settings den hittar (DONE SettingsManager)
            vilka plugins den använder (DONE PluginManager)
            När vi sedan använder en konkret logger (i command eller development) så
                kan vi välja verbosity level när vi configurerar logger (info eller debug har jag nu..)
                kan vi välja att ex skicka det loggade till chrome...
     */

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

        foreach (ComposerJsonParser::createFromFile($pathToComposerJson)->getPaths() as $path) {
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

    /*
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
            ## När detta är klart kan jag stänga issue om plugin system, #29 ##

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
            ## När detta är klart kan jag stänga issue om router package, #28 ##

        Anpassa exempel mm till den nya annotations-syntaxen
            * https://github.com/pgraham/php-annotations
            * Core måste läsa rätt från annotation, skriv klart Core samtidigt...
            * Example ska göra allt kul som inroute kan...
                (Och presentera det med trevlig html...)
            * Kom ihåg att även uppdatera README.md
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
            ## När detta är klart jag kan stänga issue #30 om DI samt #18 om require-dev ##
     */

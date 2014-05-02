<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute;

use inroute\Log\LoggerAwareInterface;
use Psr\Log\LoggerInterface;
use inroute\Plugin\PluginManager;
use inroute\Plugin\Core;
use inroute\classtools\ReflectionClassIterator;
use inroute\Compiler\CodeGenerator;
use inroute\Compiler\RouteFactory;
use inroute\Compiler\DefinitionFactory;
use inroute\Settings\SettingsManager;

/**
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Compiler implements LoggerAwareInterface
{
    private $classIterator, $plugins = array();

    public function __construct(ReflectionClassIterator $classIterator = null)
    {
        $this->classIterator = $classIterator ?: new ReflectionClassIterator;
    }

    /**
     * @todo Använd LoggerAwareTrait istället
     */
    public function getLogger()
    {
        return $this->logger ?: new NullLogger;
    }

    /**
     * @todo Använd LoggerAwareTrait istället
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    public function registerPlugin(PluginInterface $plugin)
    {
        $this->plugins[] = $plugin;
    }

    public function addPath($path)
    {
        // $this->getLogger()->info("Using path $path");
        $this->classIterator->addPath($path);
    }

    public function compile()
    {
        return (string) new CodeGenerator(
            new RouteFactory(
                new DefinitionFactory(
                    $this->classIterator,
                    $this->getPluginManager(),
                    $this->getLogger()
                )
            ),
            new ReflectionClassIterator(
                array(
                    __DIR__.'/Router',
                    __DIR__.'/ControllerInterface.php'
                )
            )
        );
    }

    /**
     * @return PluginManager
     */
    private function getPluginManager()
    {
        $settings = new SettingsManager(
            $this->classIterator,
            $this->getLogger()
        );

        $pluginManager = new PluginManager;
        $pluginManager->setLogger($this->getLogger());

        $pluginManager->registerPlugin(new Core($settings->getRootPath()));

        foreach ($settings->getPlugins() as $plugin) {
            $pluginManager->registerPlugin($plugin);
        }

        foreach ($this->plugins as $plugin) {
            $pluginManager->registerPlugin($plugin);
        }

        return $pluginManager;
    }

    /*
        Skriv test för SettingsManager

        Skriv en getClassIterator på liknande sätt som jag skrivit getPluginManager
            getPluginManager letar ju i settings
            getClassIterator ska leta i ComposerJsonWrapper...

        Att skapa kompiler blir då bara $compiler = new Compiler('/path/to/composer.json').
            och sedan $compiler->setLogger($logger);

        Efter det har jag något som funkar
            kolla att jag inte helt har gjort bort mig (bygg example...)
            är det läge att skriva om compiler mer testbart???
            ska Inroute vara en tunnare fasad till Compiler\Compiler som tar fler
                konfigurerade object per injection??
                    I så fall kan Compiler ta en konfigurerad ReflectionClassIterator
            Kanske ska getPluginManager() vara en metod hos SettingsManager()?
                return $settingsManager->registerPlugins(new PluginManager);
                eller tvärt om
                $plugin = new PluginManager(new SettingsManager(...))
                det känns bättre på något sätt!
                men det är fortfarande oklart hur göra med compiler->registerPlugin()
                    får det kanske inte vara med??
                    ja varför egentligen ha två olika sätt att registrera plugins??
                    addPath är ändå intressant eftersom folk kanske vill använda inroute
                    utan att använda composer..

        kanske behöver jag inte Log subpaketet
            (om det inte är tillräckligt många som implementerar...)
            om jag använder Trait eller inte måste göra skillnad för om jag stödjer php 5.3 eller inte
                se composer.json samt .travis.yml

        // LOGGING
            vilken composer.json den läser
                använd någon form av composer.json-wrapper...
                läs paths från autoload
            vilken fil den skriver till (bara med cli)
            vilka controllers den hittar (DONE DefinitionFactory)
            vilka settings den hittar (DONE SettingsManager)
            vilka plugins den använder (DONE PluginManager)
            plugins kan logga vad de vill
                kolla hur det blir med det som redan loggas innan jag börjar logga en massa i Core
                kanske är det onödigt att göra plugins LoggerAwware?
                    Nej det är väldigt bra för möjligheten att debugga sin site build...
            När vi sedan använder en konkret logger (i command eller development) så
                kan vi välja verbosity level när vi configurerar logger (info eller debug har jag nu..)
                kan vi välja att ex skicka det loggade till chrome...

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
     */
}

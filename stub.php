<?php

namespace itbz\inroute;

include "vendor/autoload.php";

header('Content-Type: text/plain');

$factories =  array(
    'itbz\test\Working' => array(
        array(
            'name' => '$bar',
            'class' => 'DateTime',
            'factory' => 'foobar'
        ),
        array(
            'name' => '$x',
            'class' => '',
            'factory' => 'xfactory'
        )
    )
);

/*
    fungerar inte alls om class är array, det måste jag specialbehandla någon stans..

    jag tycker att skriva mina templates på detta sätt fungerar sådär
        testa med mustache istället!

        det är också bra för mig!
 */

include "src/itbz/inroute/Template/Dependencies.php";

die();

// @inject leder till kod i den här stilen
class Dependencies
{
    private $container;
    public function __construct($container)
    {
        $this->container = $container;
    }
    function PlaskingView()
    {
        // Det här är ju inte helt Pimple. Fundera på vad jag vill kräva av världen...
        $a = $this->container['someObjectFactory']();

        // Detta test kan bli fel om projektet är i ett annat namespace
        if (!$a instanceof SomeObject) {
            $msg = 'DI-container method someObjectFactory must return a SomeObject instance.';
            //throw new DependencyExpection($msg);
            echo $msg;die();
        }

        // Detta kan också bli fel om projektet är i ett annat namespace
        return new PlaskingView($a);
    }
}


// bogus..
class Route {}
class SomeObject {}


// Ska egentligen skapas med hjälp av plask.json i plasksite.php (gateway)
/*
    I en json-konfigurationsfil så ska
        sökvägen till en fil som returnerar en Caller finnas..
        samt sökvägen till en fil som returnerar en container..
        om vad DIC returnerar ska kontrolleras ska vara en inställning

    Dessa filer samt den autogenererade koden ska laddas i en gateway
        plasksite.php (behöver inte läsa json, utan skapas från template...)

        Det ska vara upp till användaren att skriva en index.php där gateway anropas
            jag vet inte om användaren använder mod_rewrite eller någon annan lösning
            jag vill att användaren ska ha ett bra ställa att kicka in logg med
                exemplevis monolog. (Gärna med aspects!! Så tycker jag att logning
                ska implementeras, se till att mitt projekt har bra tänk för detta!!)
 */
$wwwRoot = 'någon definierad root siten använder sig av..';
$caller = new DefaultCaller;
$container = array(
    'someObjectFactory' => function(){
        return new SomeObject;
    }
);
$deps = new Dependencies($container);


// @route skapar kod i den här stilen
$actionTest = function() use ($deps, $caller) {
    $view = $deps->PlaskingView();
    
    // Ska egentligen hämtas från min router...
    // Jag vill skriva ett eget Route-object
    //      så att jag kan byta mellan olika Routers utan
    //      att användarkod behöver förändras...
    $route = new Route;
    $route->test = 'yeah';

    $caller->call(array($view, 'view'), $route);
};

// kär min lilla test..
$actionTest();

// Om jag väljer Aura-router..
$routes = array(
    $wwwRoot . 'domain' => array(
        'routes' => array(
            array(
                'path' => '/{:name}',
                'method' => array('GET'),
                'values' => array(
                    'action' => $actionTest
                )
            )
        )
    )
);

/*

    TODO:

    1) Titta på olika routers och bestämma mig för en
    2) Definiera Route-gränssnittet (och implementera för den router jag valt)
    4) Kontrollera så att allt fungerar med namespaces
    5) Autogenerera kod med hjälp av enkla templates
    6) Skriv plasksite.php
    7) Baka ihop allt i en phar att användas i build-cykel


    Hur ska koden användas i projekt?
    =================================
    
    development:

    $plask = new Plask('plask.json');
    $plask->run($requestUri, $_SERVER);

    production:

    > php plask.phar build plask.json
    $plask = include plasksite.php;
    $plask->run($requestUri, $_SERVER);

 */

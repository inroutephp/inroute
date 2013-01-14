<?php

namespace itbz\inroute;

include "vendor/autoload.php";

header('Content-Type: text/plain');

$factories =  array(
    array(
        'name' => 'itbz_test_Working',
        'class' => 'itbz\test\Working',
        'signature' => '$bar, $x',
        'params' => array(
            array(
                'name' => '$bar',
                'class' => 'DateTime',
                'array' => false,
                'factory' => 'foobar'
            ),
            array(
                'name' => '$x',
                'class' => '',
                'array' => true,
                'factory' => 'xfactory'
            )
        )
    )
);

$template = 'class Dependencies {
    private $container;
    public function __construct($container) {
        $this->container = $container;
    }
    {{#factories}}
    function {{name}}{
        {{#params}}
        {{name}} = $this->container["{{factory}}"]();
        {{#class}}
        if (!{{name}} instanceof \{{class}}) {
            throw new DependencyExpection("DI-container method \'{{factory}}\' must return a {{class}} instance.");
        }
        {{/class}}
        {{#array}}
        if (!is_array({{name}})) {
            throw new DependencyExpection("DI-container method \'{{factory}}\' must return an array.");
        }
        {{/array}}
        {{/params}}
        return new \{{class}}({{signature}});
    }
    {{/factories}}
}';


$mustache = new \Mustache_Engine;
echo $mustache->render($template, array('factories' => $factories));

die();

/*
    Generator ska skapa en fil (inroute.php) som returnerar ett object som
        är alla de genererade rutterna, och injektionerna

    Sedan är det upp till användaren att köra dispatch med en url och $_server
*/

$inroute_json = array(
    "root" => "github/inroute/",
    "caller" => "itbz\\inroute\\DefaultCaller",
    "DIC" => "project\\Container",
    "source" => "project\\Controllers"
);


// @route skapar kod i den här stilen
$actionTest = function() use ($dependencies, $caller) {
    $view = $dependencies->PlaskingView();
    
    // Ska egentligen hämtas från min router...
    // Jag vill skriva ett eget Route-object
    //      så att jag kan byta mellan olika Routers utan
    //      att användarkod behöver förändras...
    $route = new Route;
    $route->test = 'yeah';

    $caller->call(array($view, 'view'), $route);
};


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

    1) Titta på olika routers och bestämma mig för en: Aura
    2) Definiera Route-gränssnittet (och implementera för den router jag valt)
    4) Kontrollera så att allt fungerar med namespaces
    5) Autogenerera kod med hjälp av enkla templates
    7) Baka ihop allt i en phar att användas i build-cykel


    Hur ska koden användas i projekt?
    =================================
    
    development:

    $builder = new InrouteBuilder('inroute.json');
    $inroute = $builder->build();
    $inroute->dispatch($requestUri, $_SERVER);

    production:

    > php inroute.phar build inroute.json
    $inroute = include inroute.php;
    $inroute->dispatch($requestUri, $_SERVER);

 */

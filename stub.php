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

$depsTmpl = 'class Dependencies {
    private $container;
    public function __construct($container) {
        $this->container = $container;
    }
    {{#factories}}
    function {{name}}(){
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
echo $mustache->render($depsTmpl, array('factories' => $factories));

echo "\n\n";


// ROUTES, getRoutes måste returnera enligt rätt format!

$routes = array(
    array(
        'name' => 'home',
        'path' => '/',
        'method' => 'GET',
        'cntrlfactory' => 'itbz_test_Working',
        'cntrlmethod' => 'foo'
    )
);

$routeTmpl = 'function append_routes(\Aura\Router\Map $map, Dependencies $deps, CallerInterface $caller) {
    {{#routes}}
    $map->add("{{name}}", "{{root}}{{path}}", array(
        "values" => array(
            "method" => "{{method}}",
            "controller" => function ($args) use ($map, $deps, $caller) {
                $cntrl = $deps->{{cntrlfactory}}();
                $route = createRouteObj(); //TODO
                $caller->call(array($cntrl, "{{cntrlmethod}}"), $route);
            }
        )
    ));
    {{/routes}}
    return $map;
}';
echo $mustache->render($routeTmpl, array('routes' => $routes, 'root' => '/root'));

echo "\n\n";

$static = '$pimple = "do real shit!!!";' . "\n";
$static .= '$deps = new Dependencies($pimple);' . "\n";
$static .= '$caller = new DefaultCaller();' . "\n";
$static .= '$map = new \Aura\Router\Map();' . "\n";
$static .= '$map = append_routes($map, $deps, $caller);' . "\n";
$static .= 'return $map;' . "\n";

echo $static;

$source = $mustache->render($depsTmpl, array('factories' => $factories));
$source .= $mustache->render($routeTmpl, array('routes' => $routes));
$source .= $static;

$map = eval($source);

die();

/*
    Generator ska skapa en fil (inroute.php) som returnerar ett object som
        är alla de genererade rutterna, och injektionerna

    Sedan är det upp till användaren att köra dispatch med en url och $_server
*/


// Själva generator-classes ska ta dessa världen som argument
// json ska bara läsas av min phar wrapper...
$inroute_json = array(
    "root" => "github/inroute/",
    "caller" => "itbz\\inroute\\DefaultCaller",
    "DIC" => "project\\Container",
    "source" => "project\\Controllers"
);

/*

    TODO:

    2) Definiera Route-gränssnittet (och implementera för den router jag valt)
    4) Kontrollera så att allt fungerar med namespaces
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

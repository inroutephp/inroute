<?php

namespace itbz\inroute;

include "vendor/autoload.php";

header('Content-Type: text/plain');

/**
 * @todo Templates borde läsas från någon extern fil, det här blir så rörigt.
 * Men hur ska det gå till??
 */
class InrouteBuilder
{
    private $dependeciesTemplate = <<<'END'
namespace itbz\inroute;
class Dependencies {
    private $container;
    public function __construct($container) {
        $this->container = $container;
    }
    {{#factories}}
    public function {{name}}(){
        {{#params}}
        {{name}} = $this->container["{{factory}}"];
        {{#class}}
        if (!{{name}} instanceof \{{class}}) {
            throw new DependencyExpection("DI-container method '{{factory}}' must return a {{class}} instance.");
        }
        {{/class}}
        {{#array}}
        if (!is_array({{name}})) {
            throw new DependencyExpection("DI-container method '{{factory}}' must return an array.");
        }
        {{/array}}
        {{/params}}
        return new \{{class}}({{signature}});
    }
    {{/factories}}
}

END;

    private $routeTemplate = <<<'END'
function append_routes(\Aura\Router\Map $map, Dependencies $deps, CallerInterface $caller) {
    {{#routes}}
    $map->add("{{name}}", "{{root}}{{path}}", array(
        "values" => array(
            "method" => "{{method}}",
            "controller" => function ($route) use ($map, $deps, $caller) {
                $cntrl = $deps->{{cntrlfactory}}();
                $caller->call(array($cntrl, "{{cntrlmethod}}"), $route);
            }
        )
    ));
    {{/routes}}
    return $map;
}

END;

    /**
     * @todo staticTemplate ska inte fungera såhär. Istället ska en klass definieras
     * som tar Dependencies och Caller och Map som argument och heter Inroute och
     * har funktioner för dispatch. Och ett Inroute object är sedan det som ska
     * exporteras från eval().
     */
    private $staticTemplate = <<<'END'
$pimple = new \Pimple();
$pimple['foobar'] = function ($c) {
    return new \DateTime;
};
$pimple['xfactory'] = function ($c) {
    return array();
};
$pimple['xx'] = function ($c) {
    return 'xx';
};
$deps = new Dependencies($pimple);
$caller = new {{caller}}();
$map = new \Aura\Router\Map(new \Aura\Router\RouteFactory);
$map = append_routes($map, $deps, $caller);
return $map;

END;

    private $mustache;

    private $reflectionClasses = array();

    private $root = '';

    private $caller = 'DefaultCaller';

    public function __construct(\Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    public function addDir($dir)
    {
        //TODO sök igenom katalog efter php filer
        //lägg till varje fil
    }

    public function addFile($file)
    {
        //TODO sök igenom fil efter klasser
        //lägg till alla klasser
    }

    public function addClass($classname)
    {
        $this->reflectionClasses[] = new ReflectionClass($classname);

        return $this;
    }

    public function setRoot($root)
    {
        assert('is_string($root)');
        $this->root = $root;

        return $this;
    }

    public function getRoot()
    {
        return $this->root;
    }

    public function setCaller($caller)
    {
        assert('is_string($caller)');
        $this->caller = $caller;

        return $this;
    }

    public function getCaller()
    {
        return $this->caller;
    }

    public function getReflectionClasses()
    {
        return $this->reflectionClasses;
    }

    public function buildDependencyContainerCode()
    {
        $factories = array();
        foreach ($this->getReflectionClasses() as $refl) {
            $factories[] = array(
                'name' => str_replace('\\', '_', $refl->getName()),
                'class' => $refl->getName(),
                'signature' => $refl->getSignature(),
                'params' => $refl->getInjections()
            );
        }

        return $this->mustache->render(
            $this->dependeciesTemplate,
            array('factories' => $factories)
        );
    }

    /**
     * @todo ReflectionClass->getRoutes måste returnera enligt rätt form
     * @todo str_replace görs på samma sätt på två ställen. Skriv som en funktion till ReflectionClass
     */
    public function buildRouteCode()
    {
        $routes = array();
        foreach ($this->getReflectionClasses() as $refl) {
            foreach ($refl->getRoutes() as $route) {
                $routes[] = array(
                    'name' => $route['desc'],
                    'path' => '/',
                    'method' => 'GET',
                    'cntrlfactory' => str_replace('\\', '_', $refl->getName()),
                    'cntrlmethod' => $route['name']
                );
            }
        }

        return $this->mustache->render(
            $this->routeTemplate,
            array('routes' => $routes, 'root' => $this->getRoot())
        );
    }

    public function buildStaticCode()
    {
        return $this->mustache->render(
            $this->staticTemplate,
            array('caller' => $this->getCaller())
        );
    }

    public function buildCode()
    {
        return $this->buildDependencyContainerCode() . $this->buildRouteCode() . $this->buildStaticCode();
    }

    public function build()
    {
        return eval($this->buildCode());
    }
}


$builder = new InrouteBuilder(new \Mustache_Engine);
$code = $builder->addClass('itbz\test\Working')
    ->setRoot('hej')
    ->buildCode();

echo $code;die();

$map = eval($code);

// Det här ska göras i Inroute->dispatch() istället..
$route = $map->match('/', $_SERVER);
$route->values['controller']($route);

//TODO Om ingen route matchar måste något hända!! kasta undantag?



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

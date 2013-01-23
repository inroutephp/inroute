<?php namespace itbz\inroute;
class Dependencies {
    private $container;
    public function __construct(\Pimple $container) {
        $this->container = $container;
    }
    public function itbz_test_Controller(){
        $arg = $this->container["xx"];
        return new \itbz\test\Controller($arg);
    }
}
function append_routes(\Aura\Router\Map $map, Dependencies $deps, CallerInterface $caller) {
    $map->add("view", "/foo/{:name}", array(
        "values" => array(
            "method" => array(
                "GET",
            ),
            "controller" => function ($route) use ($map, $deps, $caller) {
                $cntrl = $deps->itbz_test_Controller();
                return $caller->call(array($cntrl, "view"), $route);
            }
        )
    ));
    return $map;
}
$deps = new Dependencies(new \itbz\test\Container);
$caller = new DefaultCaller();
$map = new \Aura\Router\Map(new \Aura\Router\RouteFactory);
$map = append_routes($map, $deps, $caller);
return new Inroute($map);


<?php namespace itbz\inroute;
class Dependencies {
    private $container;
    public function __construct(\Pimple $container) {
        $this->container = $container;
    }
    public function Controller(){
        $dependency = $this->container["getDependency"];
        return new \Controller($dependency);
    }
}
function append_routes(\Aura\Router\Map $map, Dependencies $deps, CallerInterface $caller) {
    $map->add("cntrl", "/foo/{:name}", array(
        "values" => array(
            "method" => array(
                "GET",
            ),
            "controller" => function ($route) use ($map, $deps, $caller) {
                $cntrl = $deps->Controller();
                return $caller->call(array($cntrl, "cntrl"), $route);
            }
        )
    ));
    return $map;
}
$deps = new Dependencies(new \Container);
$caller = new \Caller();
$map = new \Aura\Router\Map(new \Aura\Router\RouteFactory);
$map = append_routes($map, $deps, $caller);
return new Inroute($map);


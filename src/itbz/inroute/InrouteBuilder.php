<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package itbz\inroute
 */

namespace itbz\inroute;

use itbz\inroute\Exception\RuntimeExpection;
use Mustache_Engine;

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
                return $caller->call(array($cntrl, "{{cntrlmethod}}"), $route);
            }
        )
    ));
    {{/routes}}
    return $map;
}

END;

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
return new Inroute($map);

END;

    private $mustache;

    private $reflectionClasses = array();

    private $root = '';

    private $caller = 'DefaultCaller';

    public function __construct(Mustache_Engine $mustache)
    {
        $this->mustache = $mustache;
    }

    /**
     * Scan dir and process found classes
     *
     * @param string $dirname
     *
     * @return InrouteBuilder instance for chaining
     *
     * @throws RuntimeException If $dirname is not a directory
     */
    public function addDir($dirname)
    {
        if (!is_dir($dirname) or !is_readable($dirname)) {
            $msg = "'$dirname' is not a readable directory";
            throw new RuntimeExpection($msg);
        }

        $directory = new \RecursiveDirectoryIterator($dirname);
        $regexp = new \RegexIterator(
            new \RecursiveIteratorIterator($directory),
            '/^.+\.php$/i',
            \RecursiveRegexIterator::GET_MATCH
        );

        foreach ($regexp as $filename => $object) {
            if (is_readable($filename)) {
                $this->addFile($filename);
            }
        }

        return $this;
    }

    /**
     * Scan file and process found classes
     *
     * @param string $filename
     *
     * @return InrouteBuilder instance for chaining
     *
     * @throws RuntimeException If $filename is not readable
     */
    public function addFile($filename)
    {
        if (!file($filename) or !is_readable($filename)) {
            $msg = "'$filename' is not a readable file";
            throw new RuntimeExpection($msg);
        }

        $currentClasses = get_declared_classes();
        include $filename;
        $includedClasses = array_diff(get_declared_classes(), $currentClasses);
        foreach ($includedClasses as $classname) {
            $this->addClass($classname);
        }

        return $this;
    }

    /**
     * Add class for processing
     *
     * @param string $classname
     *
     * @return InrouteBuilder instance for chaining
     */
    public function addClass($classname)
    {
        $reflClass = new ReflectionClass($classname);
        if ($reflClass->isInroute()) {
            $this->reflectionClasses[] = $reflClass;
        }

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

    public function getDependencyContainerCode()
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
    public function getRouteCode()
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

    public function getStaticCode()
    {
        return $this->mustache->render(
            $this->staticTemplate,
            array('caller' => $this->getCaller())
        );
    }

    /**
     * Build inroute code
     *
     * @return string The generated code
     */
    public function build()
    {
        return $this->getDependencyContainerCode()
            . $this->getRouteCode()
            . $this->getStaticCode();
    }
}

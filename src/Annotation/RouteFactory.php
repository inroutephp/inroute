<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Annotation;

use inroutephp\inroute\Compiler\RouteCollectionInterface;
use inroutephp\inroute\Compiler\RouteCollection;
use inroutephp\inroute\Runtime\Route as RouteObject;
use Doctrine\Common\Annotations\AnnotationReader;

final class RouteFactory
{
    /**
     * @var AnnotationReader
     */
    private $reader;

    public function __construct(AnnotationReader $reader = null)
    {
        $this->reader = $reader ?: new AnnotationReader;
    }

    public function createRoutesFrom(string $classname): RouteCollectionInterface
    {
        if (!class_exists($classname)) {
            return new RouteCollection([]);
        }

        $classReflector = new \ReflectionClass($classname);

        if (!$classReflector->isInstantiable()) {
            return new RouteCollection([]);
        }

        $routes = [];

        foreach ($classReflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodReflector) {
            if ($methodReflector->isConstructor()) {
                continue;
            }

            $routes[] = new RouteObject(
                $classReflector->getName(),
                $methodReflector->getName(),
                new AnnotatedObject($this->reader->getMethodAnnotations($methodReflector))
            );
        }

        return new RouteCollection($routes);
    }
}

<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Compiler\Doctrine;

use inroutephp\inroute\Compiler\RouteFactoryInterface;
use inroutephp\inroute\Compiler\RouteCollectionInterface;
use inroutephp\inroute\Compiler\RouteCollection;
use inroutephp\inroute\Runtime\Route as RouteObject;
use Doctrine\Common\Annotations\AnnotationReader;

final class RouteFactory implements RouteFactoryInterface
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

        $classAnnotations = $this->reader->getClassAnnotations($classReflector);

        $routes = [];

        foreach ($classReflector->getMethods(\ReflectionMethod::IS_PUBLIC) as $methodReflector) {
            if ($methodReflector->isConstructor()) {
                continue;
            }

            $routes[] = new RouteObject(
                $classReflector->getName(),
                $methodReflector->getName(),
                array_merge(
                    $classAnnotations,
                    $this->reader->getMethodAnnotations($methodReflector)
                )
            );
        }

        return new RouteCollection($routes);
    }
}

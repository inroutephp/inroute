<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime;

use inroutephp\inroute\Runtime\Exception\ServiceNotFoundException;
use Psr\Container\ContainerInterface;

final class NaiveContainer implements ContainerInterface
{
    /**
     * @var array<string, mixed>
     */
    private $services = [];

    public function get($id)
    {
        $id = (string)$id;

        if (!$this->has($id)) {
            throw new ServiceNotFoundException("Unable to instantiate '$id'");
        }

        if (!isset($this->services[$id])) {
            $this->services[$id] = new $id;
        }

        return $this->services[$id];
    }

    public function has($id)
    {
        $id = (string)$id;

        if (!class_exists($id)) {
            return false;
        }

        $classReflector = new \ReflectionClass($id);

        if (!$classReflector->isInstantiable()) {
            return false;
        }

        $constructorReflector = $classReflector->getConstructor();

        if ($constructorReflector && $constructorReflector->getNumberOfRequiredParameters()) {
            return false;
        }

        return true;
    }
}

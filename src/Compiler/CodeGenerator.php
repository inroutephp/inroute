<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use inroute\classtools\ReflectionClassIteratorInterface;
use inroute\classtools\ClassMinimizer;

/**
 * Generate stand alone router code
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class CodeGenerator
{
    private $factory, $classIterator;

    /**
     * @param RouteFactory                     $factory
     * @param ReflectionClassIteratorInterface $classIterator
     */
    public function __construct(RouteFactory $factory, ReflectionClassIteratorInterface $classIterator)
    {
        $this->factory = $factory;
        $this->classIterator = $classIterator;
    }

    /**
     * @return string
     */
    public function __tostring()
    {
        return $this->generateStaticCode()
            . $this->generateRouterCode();
    }

    /**
     * @return string
     */
    private function generateRouterCode()
    {
        return "return new Router(unserialize('"
            . serialize(iterator_to_array($this->factory))
            . "'));\n";
    }

    /**
     * @return string
     */
    private function generateStaticCode()
    {
        $code = "namespace inroute\Router;\n";

        foreach ($this->classIterator as $className => $reflectedClass) {
            $code .= "if (!class_exists('$className')) {\n"
                . new ClassMinimizer($reflectedClass)
                . "\n}\n";
        }

        return $code;
    }
}

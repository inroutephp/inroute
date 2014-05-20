<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use hanneskod\classtools\FilterableClassIterator;
use hanneskod\classtools\Minimizer\ClassMinimizer;

/**
 * Generate stand alone router code
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class CodeGenerator
{
    /**
     * @var RouteFactory Routes in project
     */
    private $factory;

    /**
     * @var FilterableClassIterator Class definitions to include in generated code
     */
    private $classIterator;

    /**
     * Constructor
     *
     * @param RouteFactory            $factory
     * @param FilterableClassIterator $classIterator
     */
    public function __construct(RouteFactory $factory, FilterableClassIterator $classIterator)
    {
        $this->factory = $factory;
        $this->classIterator = $classIterator;
    }

    /**
     * Generate code
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->generateStaticCode()
            . $this->generateRouterCode();
    }

    /**
     * Get serialized router code
     *
     * @return string
     */
    private function generateRouterCode()
    {
        return "return new Router(unserialize('"
            . serialize(iterator_to_array($this->factory))
            . "'));\n";
    }

    /**
     * Get class definitions
     *
     * @return string
     */
    private function generateStaticCode()
    {
        $code = "namespace inroute\Runtime;\n";

        foreach ($this->classIterator as $className => $reflectedClass) {
            $func = $reflectedClass->isInterface() ? 'interface_exists' : 'class_exists';
            $code .= "if (!$func('$className')) {\n"
                . new ClassMinimizer($reflectedClass)
                . "\n}\n";
        }

        return $code;
    }
}

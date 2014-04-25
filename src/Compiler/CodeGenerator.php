<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

/**
 * Generate stand alone router creating code
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class CodeGenerator
{
    private $factory, $classIterator;

    /**
     * @param RouteFactory $factory
     */
    public function __construct(RouteFactory $factory, ClassIterator $classIterator = null)
    {
        $this->factory = $factory;
        $this->classIterator = $classIterator ?: new ClassIterator(array(__DIR__.'/../Router'));
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
    public function generateRouterCode()
    {
        return "return new Router(unserialize('"
            . serialize(iterator_to_array($this->factory))
            . "'));\n";
    }

    /**
     * @return string
     */
    public function generateStaticCode()
    {
        $code = "namespace inroute\Router;\n";

        foreach ($this->classIterator as $classname) {
            $code .= "if (!class_exists('$classname')) {\n"
                . new ClassMinimizer($classname)
                . "\n}\n";
        }

        return $code;
    }
}

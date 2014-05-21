<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Runtime;

/**
 * Default class instantiator
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Instantiator
{
    /**
     * Create instance of $classname using the new operator
     *
     * Pass an instantiator a runtime to enable dependency injection. The
     * instantiator must be a callable, take a string classname  as sole
     * argument and return the created object.
     *
     * @param  string $classname
     * @return mixed  Created instance
     */
    public function __invoke($classname)
    {
        return new $classname;
    }
}

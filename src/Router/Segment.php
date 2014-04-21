<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Router;

use inroute\Exception\RuntimeException;

/**
 * A segment is a named regular expression subpart of a path
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Segment
{
    private $name, $regex;

    public function __construct($name, Regex $regex)
    {
        $this->name = $name;
        $this->regex = $regex;
    }

    public function getName()
    {
        return $this->name;
    }

    public function __tostring()
    {
        return "(?<{$this->name}>{$this->regex})";
    }

    public function substitute($value)
    {
        if ($this->regex->match($value)) {
            return $value;
        }
        throw new RuntimeException("Unable to substitute token <{$this->name}> with <$value>.");
    }
}

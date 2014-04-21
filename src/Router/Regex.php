<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Router;

/**
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Regex
{
    private $regex, $matches = array();

    public function __construct($regex = '')
    {
        $this->regex = $regex ?: '[^/]+';
    }

    public function __tostring()
    {
        return $this->regex;
    }

    public function getRegex()
    {
        return "#^{$this->regex}$#";
    }

    public function match($str)
    {
        return !!preg_match($this->getRegex(), $str, $this->matches);
    }

    public function __get($key)
    {
        if (isset($this->matches[$key])) {
            return $this->matches[$key];
        }
        return '';
    }
}

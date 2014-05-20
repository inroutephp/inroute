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
 * Wrapper around preg_match
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Regex
{
    /**
     * @var string Internal regular expression
     */
    private $regex;

    /**
     * @var array Matches filled by preg_match
     */
    private $matches = array();

    /**
     * Load regex at construct
     *
     * @param string $regex Note that delimiters should not be used
     */
    public function __construct($regex = '')
    {
        $this->regex = $regex ?: '[^/]+';
    }

    /**
     * Get regex as plain string
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->regex;
    }

    /**
     * Get a complete regex with delimiters
     *
     * @return string
     */
    public function getRegex()
    {
        return "#^{$this->regex}$#";
    }

    /**
     * Match string against regular expression
     *
     * @param  string $str
     * @return bool   True if match, false otherwise
     */
    public function match($str)
    {
        return !!preg_match($this->getRegex(), $str, $this->matches);
    }

    /**
     * Read captured parameter
     *
     * @param  string $key
     * @return string
     */
    public function __get($key)
    {
        if (isset($this->matches[$key])) {
            return $this->matches[$key];
        }
        return '';
    }
}

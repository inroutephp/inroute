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
 * Defines a route environment
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Environment
{
    /**
     * @var array Loaded data
     */
    private $data;

    /**
     * Store required values at construct
     *
     * @param array $data
     */
    public function __construct(array $data = array())
    {
        $this->data = array_change_key_case($data, CASE_LOWER);
    }

    /**
     * Store data in environment
     *
     * @param  string $key
     * @param  mixed $value
     * @return void
     */
    public function set($key, $value)
    {
        $this->data[strtolower($key)] = $value;
    }

    /**
     * Read data from environment
     *
     * @param  string $key
     * @return mixed
     */
    public function get($key)
    {
        $key = strtolower($key);
        if (isset($this->data[$key])) {
            return $this->data[$key];
        }

        return '';
    }

    /**
     * Get an array representation of environment
     *
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }
}

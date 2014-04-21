<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use inroute\Router\Regex;
use inroute\Router\Segment;

/**
 * Split a path into tokens (segments and regular strings)
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Tokenizer
{
    private $segmentRegex, $tokens = array();

    /**
     * @param Regex $segmentRegex Regular expression for identifying a segment
     */
    public function __construct(Regex $segmentRegex = null)
    {
        $this->segmentRegex = $segmentRegex ?: new Regex('\{:(?<name>[a-z]+)(:\((?<regex>[^)]+)\))?\}');
    }

    /**
     * Tokenize path
     *
     * @param  string $path
     * @return array
     */
    public function tokenize($path)
    {
        $this->tokens = array();

        foreach (explode('/', $path) as $token) {
            if ($this->segmentRegex->match($token)) {
                $this->tokens[] = new Segment(
                    $this->segmentRegex->name,
                    new Regex($this->segmentRegex->regex)
                );
            } else {
                $this->tokens[] = $token;
            }
        }

        return $this->tokens;
    }

    /**
     * Get regex for last tokenization
     *
     * @return Regex
     */
    public function getRegex()
    {
        return new Regex(
            implode('/', $this->tokens)
        );
    }
}

<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace iio\inroute\Tag;

use phpDocumentor\Reflection\DocBlock;
use phpDocumentor\Reflection\DocBlock\Tag;
use iio\inroute\Exception;

/**
 * Tag base class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
abstract class AbstractTag
{
    /**
     * @var string Name of this tag
     */
    public static $name = '';

    /**
     * @var array Tag description words
     */
    protected $parts;

    /**
     * Extract tags from docblock
     *
     * Search docblock for annotations named as static member $name. All returned
     * tags will be isntances of the statically called class.
     *
     * @param  DocBlock $block
     * @return array    List of tag instances
     */
    public static function parseDocBlock(DocBlock $block)
    {
        $tags = array();
        $class = get_called_class();
        foreach ($block->getTagsByName(static::$name) as $tag) {
            $tags[] = new $class($tag);
        }

        return $tags;
    }

    /**
     * Parse path from string
     *
     * A path is enclosed in <>
     * 
     * @param  string  $str Raw string
     * @param  string  &$path Will contain the parsed path
     * @return boolean
     */
    protected static function isPath($str, &$path = '')
    {
        if (preg_match('/^<([^\s]*)>$/', trim($str), $matches)) {
            $path = $matches[1];
            return true;
        }

        return false;
    }

    /**
     * Tag base class
     *
     * @param Tag $tag
     */
    public function __construct(Tag $tag)
    {
        $this->parts = array_values(array_filter(preg_split('/\s+/', $tag->getDescription())));
    }

    /**
     * Get tag description
     *
     * @return string
     */
    public function getDescription()
    {
        return '@' . static::$name . ' ' .implode(' ', $this->parts);
    }

    /**
     * Get tag path
     *
     * @return string
     * @throws Exception If no path could be found
     */
    public function getPath()
    {
        foreach ($this->parts as $part) {
            if (self::isPath($part, $path)) {
                return $path;
            }
        }

        $msg = "Unable to parse path from tag <{$this->getDescription()}>";
        throw new Exception($msg);
    }
}

<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Compiler;

use ReflectionClass;

/**
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ClassMinimizer extends ReflectionClass
{
    /**
     * Get a minimized version of the code defining this class
     *
     * @return string
     */
    public function __tostring()
    {
        return $this->minimize();
    }

    /**
     * Get the code defining this class
     *
     * @return string
     */
    public function getPhpCode()
    {
        return implode(
            "",
            array_slice(
                file($this->getFileName()),
                $this->getStartLine() - 1,
                $this->getEndLine() - $this->getStartLine() + 1
            )
        );
    }

    /**
     * Get a minimized version of the code defining this class
     *
     * @return string
     */
    public function minimize()
    {
        return self::removeEmptyLines(
            self::removeComments(
                '<?php ' . $this->getPHPCode()
            )
        );
    }

    /**
     * Remove php comments from string
     *
     * @param  string $str
     * @return string
     */
    public static function removeComments($str)
    {
        $newStr  = '';
        $ignoreTokens = array(T_COMMENT, T_DOC_COMMENT, T_OPEN_TAG);

        foreach (token_get_all($str) as $token) {    
            if (is_array($token)) {
                if (in_array($token[0], $ignoreTokens)) {
                    continue;
                };
                $token = $token[1];
            }
            if (!empty($token)) {
                $newStr .= $token;
            }
        }

        return $newStr;
    }

    /**
     * Remove empty lines in string (lines with no visible characters)
     *
     * @param  string $str
     * @return string
     */
    public static function removeEmptyLines($str)
    {
        return implode(
            "\n",
            array_filter(
                explode("\n", $str),
                'trim'
            )
        );
    }
}

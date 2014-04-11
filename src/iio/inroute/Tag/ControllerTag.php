<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace iio\inroute\Tag;

use iio\inroute\Exception;

/**
 * Controller annotation class
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class ControllerTag extends AbstractTag
{
    /**
     * @var string Name of this tag
     */
    public static $name = 'controller';

    /**
     * Get tag path
     *
     * For the controller tag path is optional
     *
     * @return string
     */
    public function getPath()
    {
        try {
            return parent::getPath();
        } catch (Exception $e) {
            return '';
        }
    }
}

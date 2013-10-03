<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
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

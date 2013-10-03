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

class ControllerTagTest extends \PHPUnit_Framework_TestCase
{
    public function testGetPath()
    {
        $tag = $this->getMock(
            'phpDocumentor\Reflection\DocBlock\Tag',
            array(),
            array(),
            '',
            false
        );

        $controllerTag = new ControllerTag($tag);

        $this->assertEquals('', $controllerTag->getPath());
    }
}

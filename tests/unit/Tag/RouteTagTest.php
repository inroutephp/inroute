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

class RouteTagTest extends \PHPUnit_Framework_TestCase
{
    private function getTagWithDesc($desc)
    {
        $tag = $this->getMock(
            'phpDocumentor\Reflection\DocBlock\Tag',
            array('getDescription'),
            array(),
            '',
            false
        );

        $tag->expects($this->any())
            ->method('getDescription')
            ->will($this->returnValue($desc));

        return $tag;
    }

    /**
     * @expectedException \iio\inroute\Exception
     */
    public function testInvalidMethod()
    {
        new RouteTag($this->getTagWithDesc('GET,FOOBAR /www/www'));
    }

    /**
     * @expectedException \iio\inroute\Exception
     */
    public function testInvalidRouteDescription()
    {
        new RouteTag($this->getTagWithDesc(' GET '));
    }
}

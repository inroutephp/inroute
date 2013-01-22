<?php
namespace itbz\test;

/**
 * @inrouteContainer
 */
class Container extends \Pimple
{
    public function __construct()
    {
        $this['foobar'] = function ($c) {
            return new \DateTime;
        };
        $this['xfactory'] = function ($c) {
            return array();
        };
        $this['xx'] = function ($c) {
            return 'xx';
        };
    }
}

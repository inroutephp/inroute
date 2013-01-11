<?php
namespace itbz\test;

/**
 * @inroute
 */
class InjectionMissing
{
    public function __construct($a)
    {
        echo $a;
    }
}

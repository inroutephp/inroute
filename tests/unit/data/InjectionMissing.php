<?php
namespace unit\data;

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

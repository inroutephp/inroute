<?php
namespace data;

/**
 * @controller
 */
class InjectionMissing
{
    /**
     * @param void $a Inject clause missing...
     */
    public function __construct($a)
    {
        echo $a;
    }
}

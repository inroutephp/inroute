<?php
namespace unit\data;

/**
 * @controller
 */
class InjectedParameterMissing
{
    /**
     * @param string $a foobar
     */
    public function __construct()
    {
    }
}

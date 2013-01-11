<?php
namespace itbz\test;

/**
 * @inroute
 */
class InjectedParameterMissing
{
    /**
     * @inject $a foobar
     */
    public function __construct()
    {
    }
}

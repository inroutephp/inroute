<?php
namespace unit\data;

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

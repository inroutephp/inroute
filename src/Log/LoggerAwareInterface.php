<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Log;

/**
 * Adds the getLogger method to logger aware classes
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface LoggerAwareInterface extends \Psr\Log\LoggerAwareInterface
{
    /**
     * @return \Psr\Log\LoggerInterface
     */
    public function getLogger();
}

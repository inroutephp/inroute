<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Runtime;

/**
 * Defines a route post filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface PostFilterInterface
{
    /**
     * TODO Lång beskrivning av hur post filter kan fungera här..
     *
     * @param  mixed $value
     * @return mixed
     */
    public function filter($value);
}

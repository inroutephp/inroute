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
 * Defines a route pre filter
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
interface PreFilterInterface
{
    /**
     * TODO Lång beskrivning av hur pre filter kan fungera här..
     *
     * @param  Environment $env
     * @return void
     */
    public function filter(Environment $env);
}

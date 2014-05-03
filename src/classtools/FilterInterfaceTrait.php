<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\classtools;

use inroute\Exception\LogicException;

/**
 * Basic implementation of the FilterInterface
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
trait FilterInterfaceTrait
{
    private $filterable;

    /**
     * Bind filter to filterable
     *
     * @param  Filterable $filterable
     */
    public function bindFilter(Filterable $filterable)
    {
        $this->filterable = $filterable;
    }

    /**
     * Get filterable bound to filter
     *
     * @return Filterable
     * @throws LogicException If no bound filterable exists
     */
    public function getFilterable()
    {
        if (!isset($this->filterable)) {
            throw new LogicException("Filter not bound to filterable.");
        }
        return $this->filterable;
    }
}

<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

$loader = include __DIR__ . '/../src/bootstrap.php';
$loader->add('unit\data', __DIR__);

return $loader;

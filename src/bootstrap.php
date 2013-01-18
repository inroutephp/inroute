<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 *
 * @author Hannes Forsgård <hannes.forsgard@gmail.com>
 * @package itbz\inroute
 */

$loader = __DIR__ . '/../vendor/autoload.php';

if (file_exists($loader)) {
    return include $loader;
} else {
    echo 'You must set up Inroute dependencies to continue.' . PHP_EOL
        . 'Run the following commands from the project root:' . PHP_EOL
        . 'curl -s http://getcomposer.org/installer | php' . PHP_EOL
        . 'php composer.phar install' . PHP_EOL;
    exit(1);
}

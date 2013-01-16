<?php

namespace itbz\inroute;

include "vendor/autoload.php";
header('Content-Type: text/plain');

$facade = new InrouteFacade(array(
    'root' => '',
    'caller' => 'DefaultCaller',
    'container' => '\itbz\test\Container',
    // Tre stycken som kan vara både array eller sträng...
    'prefixes' => array(
        'php'
    ),
    'dirs' => array(
    ),
    'files' => 'tests/itbz/test/Working.php',
    'templatedir' => 'src/itbz/inroute/Templates'
));

$code = $facade->generate();

$inroute = eval($code);
echo $inroute->dispatch('/foo/yeah', $_SERVER);

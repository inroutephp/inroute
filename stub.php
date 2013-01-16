<?php

namespace itbz\inroute;

include "vendor/autoload.php";
header('Content-Type: text/plain');

$facade = new InrouteFacade('inroute.json');
$code = $facade->generate();

$inroute = eval($code);
echo $inroute->dispatch('/foo/yeah', $_SERVER);

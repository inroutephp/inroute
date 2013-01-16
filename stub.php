<?php

include "vendor/autoload.php";

$facade = new \itbz\inroute\InrouteFacade('inroute.json');
$inroute = eval($facade->generate());

echo $inroute->dispatch('/foo/yeah', $_SERVER);

<?php

namespace itbz\inroute;

include "vendor/autoload.php";
header('Content-Type: text/plain');

$builder = new InrouteBuilder(new \Mustache_Engine);
$code = $builder->addFile('tests/itbz/test/Working.php')
    ->setRoot('/hej')
    ->build();

$inroute = eval($code);

echo $inroute->dispatch('/hej/', $_SERVER);

// Builder ska ta dessa världen som argument, även som ett json-object...
// json ska bara läsas av min phar wrapper...
$inroute_json = array(
    "root" => "github/inroute/",
    "caller" => "itbz\\inroute\\DefaultCaller",
    "DIC" => "project\\Container",
    "source" => "project\\Controllers"
);

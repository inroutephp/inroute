<?php

namespace itbz\inroute;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Symfony\Component\Finder\Finder;

include "vendor/autoload.php";
header('Content-Type: text/plain');

$mustache = new Mustache_Engine(array(
   'loader' => new Mustache_Loader_FilesystemLoader('src/itbz/inroute/Templates')
));

$scanner = new ClassScanner(new Finder);
$scanner->addPrefix('php')
    ->addFile('tests/itbz/test/Working.php');

$generator = new RouterGenerator($mustache, $scanner);
$code = $generator->setRoot('/hej')
    ->generate();

$inroute = eval($code);

echo $inroute->dispatch('/hej/', $_SERVER);


// Builder ska ta dessa världen som argument, även som ett json-object...
//      varför inte ett loader subpaket
//      en FilesystemLoader (injecta ett Finder object!)
//      och en JsonLoader
// json ska bara läsas av min phar wrapper...
$inroute_json = array(
    "root" => "github/inroute/",
    "caller" => "itbz\\inroute\\DefaultCaller",
    "DIC" => "project\\Container",
    "source" => "project\\Controllers"
);

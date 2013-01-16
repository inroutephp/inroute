<?php

namespace itbz\inroute;

use Mustache_Engine;
use Mustache_Loader_FilesystemLoader;
use Symfony\Component\Finder\Finder;

include "vendor/autoload.php";
header('Content-Type: text/plain');

class Cont extends \Pimple
{
    public function __construct()
    {
        $this['foobar'] = function ($c) {
            return new \DateTime;
        };
        $this['xfactory'] = function ($c) {
            return array();
        };
        $this['xx'] = function ($c) {
            return 'xx';
        };
    }
}

$settings = array(
    'wwwroot' => '',
    'caller' => 'DefaultCaller',
    'container' => 'Cont',
    // Tre stycken som kan vara både array eller sträng...
    'prefixes' => array(
        'php'
    ),
    'dirs' => array(
    ),
    'files' => 'tests/itbz/test/Working.php',
    'templatesdir' => 'src/itbz/inroute/Templates'
);

$mustache = new Mustache_Engine(array(
   'loader' => new Mustache_Loader_FilesystemLoader($settings['templatesdir'])
));

$scanner = new ClassScanner(new Finder);

foreach ((array) $settings['prefixes'] as $prefix) {
    $scanner->addPrefix($prefix);
}

foreach ((array) $settings['dirs'] as $dirname) {
    $scanner->addDir($dirname);
}

foreach ((array) $settings['files'] as $filename) {
    $scanner->addFile($filename);
}

$generator = new RouterGenerator($mustache, $scanner);
$code = $generator->setRoot($settings['wwwroot'])
    ->setCaller($settings['caller'])
    ->setContainer($settings['container'])
    ->generate();



$inroute = eval($code);

echo $inroute->dispatch('/foo/yeah', $_SERVER);

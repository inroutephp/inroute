<?php

namespace itbz\inroute;

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

$facade = new InrouteFacade(array(
    'root' => '',
    'caller' => 'DefaultCaller',
    'container' => 'Cont',
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

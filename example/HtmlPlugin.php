<?php
namespace inroute\example;

use inroute\Plugin\PluginInterface;
use inroute\Compiler\Definition;

class HtmlPlugin implements PluginInterface
{
    use \Psr\Log\LoggerAwareTrait;

    public function processRouteDefinition(Definition $def)
    {
        $def->addPostFilter('inroute\example\HtmlFilter');
    }
}

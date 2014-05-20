<?php
namespace inroute\example;

use inroute\Runtime\PostFilterInterface;

class HtmlFilter implements PostFilterInterface
{
    public function filter($value)
    {
        return "<strong>$value</strong>";
    }
}

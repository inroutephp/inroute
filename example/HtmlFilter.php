<?php
namespace inroute\example;

use inroute\Router\PostFilterInterface;

class HtmlFilter implements PostFilterInterface
{
    public function filter($value)
    {
        return "<strong>$value</strong>";
    }
}

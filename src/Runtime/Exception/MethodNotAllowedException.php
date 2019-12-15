<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Exception;

use Psr\Http\Message\ServerRequestInterface;

class MethodNotAllowedException extends RequestException
{
    /**
     * @param array<string, mixed> $context
     */
    public function __construct(ServerRequestInterface $request, array $context = [])
    {
        parent::__construct("Http method not allowd", 405, $request, $context);
    }
}

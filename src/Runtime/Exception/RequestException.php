<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Exception;

use inroutephp\inroute\Exception;
use Psr\Http\Message\ServerRequestInterface;

class RequestException extends \RuntimeException implements Exception
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $context;

    public function __construct(string $message, int $code, ServerRequestInterface $request, array $context = [])
    {
        parent::__construct($message, $code);
        $this->request = $request;
        $this->context = $context;
    }

    public function getRequest(): ServerRequestInterface
    {
        return $this->request;
    }

    public function getContext(): array
    {
        return $this->context;
    }
}

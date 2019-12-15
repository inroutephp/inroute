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
     * @var array<string, mixed>
     */
    private $context;

    /**
     * @param array<string, mixed> $context
     */
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

    /**
     * @return array<string, mixed>
     */
    public function getContext(): array
    {
        return $this->context;
    }
}

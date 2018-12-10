<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Exception;

use Psr\Http\Message\ServerRequestInterface;

class RequestException extends RuntimeException
{
    /**
     * @var ServerRequestInterface
     */
    private $request;

    /**
     * @var array
     */
    private $context;

    public function __construct(ServerRequestInterface $request, array $context = [])
    {
        parent::__construct();
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

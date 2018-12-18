<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Exception;

use inroutephp\inroute\Exception;
use Psr\Container\NotFoundExceptionInterface;

/**
 * Exception thrown when a service is not found in a container
 */
class ServiceNotFoundException extends \RuntimeException implements Exception, NotFoundExceptionInterface
{
}

<?php

declare(strict_types = 1);

namespace inroutephp\inroute\Runtime\Exception;

/**
 * Thrown if a non recoverable error occurs during dispatch
 */
class DispatchException extends \LogicException implements \inroutephp\inroute\Exception
{
}

<?php

declare(strict_types = 1);

namespace inroutephp\inroute;

use inroutephp\inroute\Runtime\Exception\CompatibilityException;

final class Package
{
    /**
     * Current package version number
     */
    const VERSION = "1.1.1";

    /**
     * The lowest version number this runtime is compatible with
     */
    private const LOWEST_SUPPORTED_VERSION = '1.0.0-beta6';

    /**
     * The lowest version number this runtime is not compatible with
     */
    private const LOWEST_UNSUPPORTED_VERSION = '2';

    /**
     * Error message template
     */
    private const ERROR_MSG = 'Unable to read router compiled with inroute version %s using runtime version %s.';

    /**
     * Validate that version number is supported by the runtime
     */
    public static function validateVersion(string $version): void
    {
        if (version_compare($version, self::LOWEST_SUPPORTED_VERSION, '<')) {
            throw new CompatibilityException(sprintf(self::ERROR_MSG, $version, self::VERSION));
        }

        if (version_compare($version, self::LOWEST_UNSUPPORTED_VERSION, '>=')) {
            throw new CompatibilityException(sprintf(self::ERROR_MSG, $version, self::VERSION));
        }
    }
}

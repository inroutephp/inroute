<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Console;

use Psr\Log\LoggerInterface;
use Psr\Log\LoggerTrait;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Simple console logger
 *
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class Logger implements LoggerInterface
{
    use LoggerTrait;

    /**
     * @var array Map Psr\Log\LogLevel constants to comparable integers
     */
    private static $logLevelMap = [
        \Psr\Log\LogLevel::EMERGENCY => 1,
        \Psr\Log\LogLevel::ALERT => 2,
        \Psr\Log\LogLevel::CRITICAL => 3,
        \Psr\Log\LogLevel::ERROR => 4,
        \Psr\Log\LogLevel::WARNING => 5,
        \Psr\Log\LogLevel::NOTICE => 6,
        \Psr\Log\LogLevel::INFO => 7,
        \Psr\Log\LogLevel::DEBUG => 8
    ];

    /**
     * @var string Choosen log level
     */
    private $level;

    /**
     * @var OutputInterface Logg target
     */
    private $output;

    /**
     * Constructor
     *
     * @param string          $level  Psr\Log\LogLevel constant
     * @param OutputInterface $output Console output
     */
    public function __construct($level, OutputInterface $output)
    {
        $this->level = self::$logLevelMap[$level];
        $this->output = $output;
    }

    /**
     * Logs with an arbitrary level.
     *
     * @param  mixed $level
     * @param  string $message
     * @param  array $context
     * @return null
     */
    public function log($level, $message, array $context = array())
    {
        if (self::$logLevelMap[$level] <= $this->level) {
            $this->output->writeln($message);
        }
    }
}

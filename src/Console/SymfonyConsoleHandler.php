<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Console;

use Monolog\Handler\AbstractProcessingHandler;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Log to symfony console
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class SymfonyConsoleHandler extends AbstractProcessingHandler
{
    /**
     * @var OutputInterface Symfony console output
     */
    private $output;

    /**
     * Setup handler
     *
     * @param OutputInterface  $output
     * @param integer          $level  The minimum logging level at which this handler will be triggered
     * @param Boolean          $bubble Whether the messages that are handled can bubble up the stack or not
     */
    public function __construct(OutputInterface $output, $level, $bubble = true)
    {
        parent::__construct($level, $bubble);
        $this->output = $output;
    }

    /**
     * Write to handler
     *
     * @param  array  $record
     * @return void
     */
    protected function write(array $record)
    {
        $this->output->write((string) $record['formatted']);
    }
}

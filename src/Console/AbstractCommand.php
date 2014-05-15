<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\LogLevel;

/**
 * Inroute base command
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
abstract class AbstractCommand extends Command
{
    /**
     * @var LoggerInterface Console logger
     */
    private $logger;

    /**
     * Configure this command. Called by console Application.
     *
     * @return void
     */
    protected function configure()
    {
        $this->addOption(
            'composer-path',
            'c',
            InputOption::VALUE_REQUIRED,
            'Path to composer.json',
            'composer.json'
        )
        ->addOption(
            'no-composer',
            null,
            InputOption::VALUE_NONE,
            'Skip parsing composer.json'
        )
        ->addOption(
            'output',
            'o',
            InputOption::VALUE_REQUIRED,
            'Save generated output to file',
            'router.php'
        )
        ->addOption(
            'path',
            'p',
            InputOption::VALUE_REQUIRED | InputOption::VALUE_IS_ARRAY,
            'Path(s) to scan for inroute classes'
        );
    }

    /**
     * Read command line options
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return array
     */
    protected function getOptions(InputInterface $input, OutputInterface $output)
    {
        $outFname = $input->getOption('output');
        $this->getLogger($output)->info("Using outfile <$outFname>");

        $composer = $input->getOption('composer-path');
        if ($input->getOption('no-composer')) {
            $this->getLogger($output)->info("Ignoring composer.json");
            $composer = '';
        }

        return [
            'output' => $outFname,
            'composer' => $composer,
            'paths' => (array)$input->getOption('path')
        ];
    }

    /**
     * Get system logger
     *
     * @param  OutputInterface $output
     * @return LoggerInterface
     */
    protected function getLogger(OutputInterface $output)
    {
        if (!isset($this->logger)) {
            $level = LogLevel::INFO;

            if (OutputInterface::VERBOSITY_VERBOSE <= $output->getVerbosity()) {
                $level = LogLevel::DEBUG;
            }

            $this->logger = new Logger($level, $output);
        }

        return $this->logger;
    }
}

<?php
/**
 * This file is part of the inroute package
 *
 * Copyright (c) 2013 Hannes Forsgård
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace iio\inroute\Console;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use iio\inroute\InrouteFactory;

/**
 * Build inroute project command
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class BuildCommand extends Command
{
    /**
     * Configure this command. Called by console Application.
     *
     * @return void
     */
    protected function configure()
    {
        $this
            ->setName('build')
            ->setDescription('Build inroute project')
            ->addArgument(
                'dir',
                InputArgument::REQUIRED,
                'Source directory to scan'
            )
            ->addOption(
                'dir',
                null,
                InputOption::VALUE_OPTIONAL | InputOption::VALUE_IS_ARRAY,
                'Use this option to add extra directories'
            )
            ->addOption(
                'root',
                null,
                InputOption::VALUE_OPTIONAL,
                'Root path to service'
            );
    }

    /**
     * Excecute this command
     *
     * @param  InputInterface  $input
     * @param  OutputInterface $output
     * @return void
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $factory = new InrouteFactory();

        $dirs = $input->getOption('dir');
        array_unshift($dirs, $input->getArgument('dir'));
        $factory->setDirs($dirs);

        $root = $input->getOption('root');
        if ($root) {
            $factory->setRoot();
        }

        $code = $factory->generate();
        $output->writeln('<?php ' . $code);
    }
}

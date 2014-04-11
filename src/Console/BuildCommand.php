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
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use inroute\InrouteFactory;

/**
 * Build inroute project command
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class BuildCommand extends Command
{
    /**
     * @var Loader Composer autoloader
     */
    private $loader;

    /**
     * Build inroute project command
     *
     * @param Loader $loader Composer autoloader
     */
    public function __construct($loader)
    {
        parent::__construct();
        $this->loader = $loader;
    }

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

        $dir = $input->getArgument('dir');
        $factory->addDirs((array) $dir);
        $this->loader->add('', $dir);

        $root = $input->getOption('root');
        if ($root) {
            $factory->setRoot($root);
        }
        
        $code = $factory->generate();
        $output->writeln('<?php ' . $code);
    }
}

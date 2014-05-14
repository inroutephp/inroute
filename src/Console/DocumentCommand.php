<?php
/**
 * This program is free software. It comes without any warranty, to
 * the extent permitted by applicable law. You can redistribute it
 * and/or modify it under the terms of the Do What The Fuck You Want
 * To Public License, Version 2, as published by Sam Hocevar. See
 * http://www.wtfpl.net/ for more details.
 */

namespace inroute\Console;

use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * Generate swagger documentation for inroute project
 * 
 * @author Hannes Forsgård <hannes.forsgard@fripost.org>
 */
class DocumentCommand extends AbstractCommand
{
    /**
     * Configure this command. Called by console Application.
     *
     * @return void
     */
    protected function configure()
    {
        $this->setName('document')->setDescription('Generate swagger documentation');
        parent::configure();
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
        $this->getOptions($input, $output);
        throw new \Exception("Not implemented..");
    }
}

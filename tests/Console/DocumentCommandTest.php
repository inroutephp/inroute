<?php
namespace inroute\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class DocumentCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new DocumentCommand());

        $command = $application->find('document');
        $commandTester = new CommandTester($command);

        $this->setExpectedException('Exception');
        $commandTester->execute([]);
    }
}

<?php
namespace inroute\Console;

use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;
use Symfony\Component\Console\Output\OutputInterface;

class BuildCommandTest extends \PHPUnit_Framework_TestCase
{
    public function testExecute()
    {
        $application = new Application();
        $application->add(new BuildCommand());

        $targetFileName = tempnam(sys_get_temp_dir(), 'inroute');

        $command = $application->find('build');
        $commandTester = new CommandTester($command);
        $commandTester->execute(
            [
                '--output' => $targetFileName,
                '--no-composer',
                '--path' => __DIR__ . '/../../example'
            ],
            [
                'verbosity' => OutputInterface::VERBOSITY_VERBOSE
            ]
        );

        $this->assertTrue(!!file_get_contents($targetFileName));
        unlink($targetFileName);

        $this->assertTrue(!!$commandTester->getDisplay());
    }
}

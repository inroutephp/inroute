<?php
namespace inroute\Console;

class LoggerTest extends \PHPUnit_Framework_TestCase
{
    public function testIgnoreLowLevel()
    {
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
        $logger = new Logger(\Psr\Log\LogLevel::ERROR, $output);
        $logger->info('this-is-not-logged');
    }

    public function testLogHighLevel()
    {
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln')->once()->with('This-is-logged');
        $logger = new Logger(\Psr\Log\LogLevel::ERROR, $output);
        $logger->alert('This-is-logged');
    }

    public function testLogSameLevel()
    {
        $output = \Mockery::mock('Symfony\Component\Console\Output\OutputInterface');
        $output->shouldReceive('writeln')->once()->with('This-is-logged');
        $logger = new Logger(\Psr\Log\LogLevel::ERROR, $output);
        $logger->error('This-is-logged');
    }
}

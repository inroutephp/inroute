<?php
namespace inroute;

class InrouteFactoryTest extends \PHPUnit_Framework_TestCase
{
    public function testSetGetLogger()
    {
        $factory = new InrouteFactory;
        $this->assertInstanceOf('Psr\Log\NullLogger', $factory->getLogger());

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $factory->setLogger($logger);
        $this->assertSame($logger, $factory->getLogger());
    }

    public function testErrorWhenComposerJsonIsMissing()
    {
        $factory = new InrouteFactory;

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('warning')->once();
        $factory->setLogger($logger);

        $factory->parseComposerJson('does-not-exist');
    }

    public function testAddPathsFromComposerJson()
    {
        $iterator = \Mockery::mock('hanneskod\classtools\ClassIterator');
        $iterator->shouldReceive('addPath')->atLeast()->times(1);

        $factory = new InrouteFactory($iterator);

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('info')->atLeast()->times(2);
        $factory->setLogger($logger);

        $factory->parseComposerJson(__DIR__ . '/../composer.json');
    }

    public function testErrorWhenAddingUnexistingPath()
    {
        $iterator = \Mockery::mock('hanneskod\classtools\ClassIterator');
        $iterator->shouldReceive('addPath')->once()->andThrow(new \hanneskod\classtools\Exception\RuntimeException);

        $factory = new InrouteFactory($iterator);

        $logger = \Mockery::mock('Psr\Log\LoggerInterface');
        $logger->shouldReceive('error')->once();
        $factory->setLogger($logger);

        $factory->addPath('does-not-exist');
    }

    public function testCreateCompiler()
    {
        $factory = new InrouteFactory;
        $this->assertInstanceOf('inroute\Compiler\Compiler', $factory->createCompiler());
    }

    public function testGenerateCode()
    {
        $factory = new InrouteFactory;
        $this->assertTrue(!!$factory->generate());
    }
}

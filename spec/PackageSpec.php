<?php

declare(strict_types = 1);

namespace spec\inroutephp\inroute;

use inroutephp\inroute\Package;
use inroutephp\inroute\Runtime\Exception\CompatibilityException;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class PackageSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(Package::CLASS);
    }

    function it_is_a_valid_version()
    {
        $this->validateVersion(Package::VERSION)->shouldReturn(null);
    }

    function it_does_not_validate_to_old_versions()
    {
        $this->shouldThrow(CompatibilityException::CLASS)->during('validateVersion', ['0.1']);
    }

    function it_does_not_validate_to_new_versions()
    {
        $this->shouldThrow(CompatibilityException::CLASS)->during('validateVersion', ['2.0.0']);
    }
}

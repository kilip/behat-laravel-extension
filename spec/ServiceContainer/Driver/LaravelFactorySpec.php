<?php

namespace spec\Behat\LaravelExtension\ServiceContainer\Driver;

use Behat\LaravelExtension\ServiceContainer\Driver\LaravelFactory;
use PhpSpec\ObjectBehavior;

class LaravelFactorySpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LaravelFactory::class);
    }
}

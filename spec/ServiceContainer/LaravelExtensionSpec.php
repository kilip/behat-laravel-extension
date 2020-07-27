<?php

namespace spec\Behat\LaravelExtension\ServiceContainer;

use Behat\LaravelExtension\ServiceContainer\Driver\LaravelFactory;
use Behat\LaravelExtension\ServiceContainer\LaravelExtension;
use Behat\MinkExtension\ServiceContainer\MinkExtension;
use Behat\Testwork\ServiceContainer\Extension;
use Behat\Testwork\ServiceContainer\ExtensionManager;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelExtensionSpec extends ObjectBehavior
{
    function it_is_initializable()
    {
        $this->shouldHaveType(LaravelExtension::class);
    }

    function it_should_be_a_behat_extension()
    {
        $this->shouldImplement(Extension::class);
    }

    function it_should_returns_config_key()
    {
        $this->getConfigKey()->shouldReturn('laravel');
    }

    function it_should_register_mink_driver_for_laravel(
        MinkExtension $mink
    )
    {
        $mink->getConfigKey()->shouldBeCalled()->willReturn('mink');
        $mink->registerDriverFactory(Argument::type(LaravelFactory::class))
            ->shouldBeCalled();

        $manager = new ExtensionManager([$mink->getWrappedObject()]);

        $this->initialize($manager);
    }


}

<?php

/*
 * This file is part of the Behat\LaravelExtension project.
 *
 * (c) Anthonius Munthi <https://itstoni.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

declare(strict_types=1);

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
    public function it_is_initializable()
    {
        $this->shouldHaveType(LaravelExtension::class);
    }

    public function it_should_be_a_behat_extension()
    {
        $this->shouldImplement(Extension::class);
    }

    public function it_should_returns_config_key()
    {
        $this->getConfigKey()->shouldReturn('laravel');
    }

    public function it_should_register_mink_driver_for_laravel(
        MinkExtension $mink
    ) {
        $mink->getConfigKey()->shouldBeCalled()->willReturn('mink');
        $mink->registerDriverFactory(Argument::type(LaravelFactory::class))
            ->shouldBeCalled();

        $manager = new ExtensionManager([$mink->getWrappedObject()]);

        $this->initialize($manager);
    }
}

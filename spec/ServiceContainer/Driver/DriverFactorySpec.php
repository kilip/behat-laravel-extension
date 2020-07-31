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

namespace spec\Behat\LaravelExtension\ServiceContainer\Driver;

use Behat\LaravelExtension\Driver\KernelDriver;
use Behat\LaravelExtension\ServiceContainer\Driver\DriverFactory;
use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory as DriverFactoryContract;
use PhpSpec\ObjectBehavior;
use Symfony\Component\DependencyInjection\Definition;

class DriverFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(DriverFactory::class);
    }

    public function it_should_be_a_mink_driver_factory()
    {
        $this->shouldImplement(DriverFactoryContract::class);
    }

    public function its_driver_name_should_be_laravel()
    {
        $this->getDriverName()->shouldReturn('laravel');
    }

    public function it_should_not_support_javascript()
    {
        $this->supportsJavascript()->shouldReturn(false);
    }

    public function it_should_build_mink_browserkit_driver()
    {
        $definition = $this->buildDriver([]);

        $definition->shouldBeAnInstanceOf(Definition::class);
        $definition->getClass()->shouldReturn(KernelDriver::class);

        /** @var \Symfony\Component\DependencyInjection\Reference $argument */
        $argument = $definition->getArgument(0);
        $argument->__toString()->shouldReturn('laravel.factory.app');
    }
}

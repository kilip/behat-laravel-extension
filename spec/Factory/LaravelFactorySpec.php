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

namespace spec\Behat\LaravelExtension\Factory;

use Behat\LaravelExtension\Contracts\LaravelFactoryContract;
use Behat\LaravelExtension\Factory\LaravelFactory;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;
use Illuminate\Foundation\Application;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class LaravelFactorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(
            __DIR__.'/../../vendor/laravel/laravel/bootstrap/app.php'
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LaravelFactory::class);
    }

    public function it_should_be_laravel_factory()
    {
        $this->shouldImplement(LaravelFactoryContract::class);
    }

    public function it_should_boot_application(
        SpecLaravelFactoryCallback $callback
    ) {
        $callback->created(Argument::type(ApplicationContract::class))->shouldBeCalledOnce();

        $this->boot();
        $this->afterApplicationCreated([$callback, 'created']);

        $this->getApplication()
            ->shouldBeAnInstanceOf(Application::class);
    }

    public function it_should_tearDown_application(SpecLaravelFactoryCallback $callback)
    {
        $callback->destroyed(Argument::type(ApplicationContract::class))->shouldBeCalledOnce();

        $this->beforeApplicationDestroyed([$callback, 'destroyed']);
        $this->boot();
        $this->getApplication()->shouldBeAnInstanceOf(ApplicationContract::class);
        $this->tearDown();
    }

    public function it_should_reboot_application(
        SpecLaravelFactoryCallback $callback
    ) {
        $callback->created(Argument::any())
            ->shouldBeCalledTimes(2);
        $callback->destroyed(Argument::any())
            ->shouldBeCalledTimes(1);

        $this->afterApplicationCreated([$callback, 'created']);
        $this->beforeApplicationDestroyed([$callback, 'destroyed']);

        $this->boot();
        $this->reboot();
    }
}

class SpecLaravelFactoryCallback
{
    public function created()
    {
    }

    public function destroyed()
    {
    }
}

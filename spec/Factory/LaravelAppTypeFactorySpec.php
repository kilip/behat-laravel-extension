<?php

namespace spec\Behat\LaravelExtension\Factory;

use Behat\LaravelExtension\Contracts\LaravelFactoryContract;
use Behat\LaravelExtension\Factory\LaravelAppTypeFactory;
use Illuminate\Foundation\Application;
use PhpSpec\ObjectBehavior;

class LaravelAppTypeFactorySpec extends ObjectBehavior
{
    function let()
    {
        $this->beConstructedWith(__DIR__.'/Resources/app.php');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LaravelAppTypeFactory::class);
    }

    function it_should_be_a_laravel_factory()
    {
        $this->shouldImplement(LaravelFactoryContract::class);
    }

    function it_should_creates_application()
    {
        $this->boot();
        $app = $this();
        $app->shouldHaveType(Application::class);
    }
}

<?php

namespace spec\Behat\LaravelExtension\Factory;

use Behat\LaravelExtension\Factory\LaravelPackageTypeFactory;
use Illuminate\Config\Repository;
use Illuminate\Foundation\Application;
use PhpSpec\ObjectBehavior;
use Tests\DummyPackage\DummyService;
use Tests\DummyPackage\DummyServiceProvider;
use Webmozart\Assert\Assert;

class LaravelPackageTypeFactorySpec extends ObjectBehavior
{
    private $providers = [
        DummyServiceProvider::class
    ];

    private $aliases = [
        'Dummy' => DummyService::class
    ];

    private $environment = [
        'key' => 'value'
    ];

    function let()
    {
        $this->beConstructedWith(
            $this->providers,
            $this->aliases,
            $this->environment
        );
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(LaravelPackageTypeFactory::class);
    }

    function it_should_be_a_laravel_factory()
    {
        $this->shouldImplement(LaravelPackageTypeFactory::class);
    }

    function it_should_create_application()
    {
        $this->boot();
        $app = $this();
        $app->providerIsLoaded(DummyServiceProvider::class)
            ->shouldReturn(true);
        $app->get('config')->shouldHaveType(Repository::class);
        $app->get('config')->get('key')->shouldReturn('value');
        $app->make('Dummy')->shouldHaveType(DummyService::class);
    }
}

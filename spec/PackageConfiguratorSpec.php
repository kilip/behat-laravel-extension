<?php

namespace spec\Behat\LaravelExtension;

use Behat\LaravelExtension\PackageConfigurator;
use Illuminate\Foundation\Testing\Concerns\InteractsWithContainer;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\Assert;
use Tests\DummyPackage\DummyService;
use Tests\DummyPackage\DummyServiceProvider;

class PackageConfiguratorSpec extends ObjectBehavior
{
    function let()
    {
        $providers = [
            DummyServiceProvider::class
        ];
        $aliases = [
            'Dummy' => DummyService::class
        ];
        $this->beConstructedWith($providers, $aliases);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(PackageConfigurator::class);
    }

    function it_should_boot_laravel_application()
    {
        $this->boot();
        $object = $this->getWrappedObject();
        $app = $object->createApplication();
        Assert::assertContains(DummyServiceProvider::class, $app->getLoadedProviders());
        Assert::isInstanceOf(DummyService::class, $object = $app->make('Dummy'));
    }
}

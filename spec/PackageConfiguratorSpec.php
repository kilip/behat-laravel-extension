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

namespace spec\Behat\LaravelExtension;

use Behat\LaravelExtension\PackageConfigurator;
use PhpSpec\ObjectBehavior;
use PHPUnit\Framework\Assert;
use Tests\DummyPackage\DummyService;
use Tests\DummyPackage\DummyServiceProvider;

class PackageConfiguratorSpec extends ObjectBehavior
{
    public function let()
    {
        $providers = [
            DummyServiceProvider::class,
        ];
        $aliases = [
            'Dummy' => DummyService::class,
        ];
        $this->beConstructedWith($providers, $aliases);
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(PackageConfigurator::class);
    }

    public function it_should_boot_laravel_application()
    {
        $this->boot();
        $object = $this->getWrappedObject();
        $app = $object->createApplication();
        Assert::assertContains(DummyServiceProvider::class, $app->getLoadedProviders());
        Assert::isInstanceOf(DummyService::class, $object = $app->make('Dummy'));
    }
}

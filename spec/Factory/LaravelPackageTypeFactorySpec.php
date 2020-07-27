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

use Behat\LaravelExtension\Factory\LaravelPackageTypeFactory;
use Illuminate\Config\Repository;
use PhpSpec\ObjectBehavior;
use Tests\DummyPackage\DummyService;
use Tests\DummyPackage\DummyServiceProvider;

class LaravelPackageTypeFactorySpec extends ObjectBehavior
{
    private $providers = [
        DummyServiceProvider::class,
    ];

    private $aliases = [
        'Dummy' => DummyService::class,
    ];

    private $environment = [
        'key' => 'value',
    ];

    public function let()
    {
        $this->beConstructedWith(
            $this->providers,
            $this->aliases,
            $this->environment
        );
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LaravelPackageTypeFactory::class);
    }

    public function it_should_be_a_laravel_factory()
    {
        $this->shouldImplement(LaravelPackageTypeFactory::class);
    }

    public function it_should_create_application()
    {
        $this->boot();
        $app = $this->getApplication();
        $app->getProvider(DummyServiceProvider::class)->shouldHaveType(DummyServiceProvider::class);
        $app->get('config')->shouldHaveType(Repository::class);
        $app->get('config')->get('key')->shouldReturn('value');
        $app->make('Dummy')->shouldHaveType(DummyService::class);
    }
}

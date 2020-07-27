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
use Behat\LaravelExtension\Factory\LaravelAppTypeFactory;
use Illuminate\Foundation\Application;
use PhpSpec\ObjectBehavior;

class LaravelAppTypeFactorySpec extends ObjectBehavior
{
    public function let()
    {
        $this->beConstructedWith(__DIR__.'/Resources/app.php');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(LaravelAppTypeFactory::class);
    }

    public function it_should_be_a_laravel_factory()
    {
        $this->shouldImplement(LaravelFactoryContract::class);
    }

    public function it_should_creates_application()
    {
        $this->boot();
        $this->getApplication()->shouldHaveType(Application::class);
    }
}

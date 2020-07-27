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

use Behat\LaravelExtension\ServiceContainer\Driver\LaravelFactory;
use PhpSpec\ObjectBehavior;

class LaravelFactorySpec extends ObjectBehavior
{
    public function it_is_initializable()
    {
        $this->shouldHaveType(LaravelFactory::class);
    }
}

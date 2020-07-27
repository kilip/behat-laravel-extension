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

namespace spec\Behat\LaravelExtension\Driver;

use Behat\LaravelExtension\Driver\KernelDriver;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class KernelDriverSpec extends ObjectBehavior
{
    public function let(
        HttpKernelInterface $app
    ) {
        $this->beConstructedWith($app, 'http://localhost/');
    }

    public function it_is_initializable()
    {
        $this->shouldHaveType(KernelDriver::class);
    }

    public function it_should_create_client()
    {
        $this->getClient()->getServerParameter('SCRIPT_FILENAME', '/');
    }
}

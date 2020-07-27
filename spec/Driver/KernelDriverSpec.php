<?php

namespace spec\Behat\LaravelExtension\Driver;

use Behat\LaravelExtension\Driver\KernelDriver;
use PhpSpec\ObjectBehavior;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class KernelDriverSpec extends ObjectBehavior
{
    function let(
        HttpKernelInterface $app
    )
    {
        $this->beConstructedWith($app, 'http://localhost/');
    }

    function it_is_initializable()
    {
        $this->shouldHaveType(KernelDriver::class);
    }

    function it_should_create_client()
    {
        $this->getClient()->shouldHaveType(HttpKernelBrowser::class);
        $this->getClient()->getServerParameter('SCRIPT_FILENAME','/');
    }
}

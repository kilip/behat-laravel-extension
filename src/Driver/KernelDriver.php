<?php

namespace Behat\LaravelExtension\Driver;

use Behat\Mink\Driver\BrowserKitDriver;
use Illuminate\Foundation\Application;
use Symfony\Component\HttpKernel\HttpKernelBrowser;
use Symfony\Component\HttpKernel\HttpKernelInterface;
use Symfony\Component\HttpKernel\KernelInterface;

class KernelDriver extends BrowserKitDriver
{
    /**
     * KernelDriver constructor.
     * @param HttpKernelInterface $app
     * @param null|string $baseUrl
     */
    public function __construct(HttpKernelInterface $app, $baseUrl = null)
    {
        $class = 'Symfony\\Component\\HttpKernel\\Client';
        if(class_exists($test = 'Symfony\\Component\\HttpKernel\\HttpKernelBrowser')){
            $class = $test;
        }
        parent::__construct(new $class($app), $baseUrl);
    }
}

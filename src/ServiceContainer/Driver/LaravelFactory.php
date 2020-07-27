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

namespace Behat\LaravelExtension\ServiceContainer\Driver;

use Behat\LaravelExtension\Driver\KernelDriver;
use Behat\LaravelExtension\ServiceContainer\LaravelExtension;
use Behat\MinkExtension\ServiceContainer\Driver\DriverFactory;
use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;

class LaravelFactory implements DriverFactory
{
    public function getDriverName()
    {
        return 'laravel';
    }

    public function supportsJavascript()
    {
        return false;
    }

    public function configure(ArrayNodeDefinition $builder)
    {
    }

    public function buildDriver(array $config)
    {
        if (!class_exists('Behat\Mink\Driver\BrowserKitDriver')) {
            throw new \RuntimeException('Install MinkBrowserKitDriver in order to use the laravel driver.');
        }

        return new Definition(KernelDriver::class, [
            new Reference(LaravelExtension::KERNEL_SERVICE_ID),
            '%mink.base_url%',
        ]);
    }
}

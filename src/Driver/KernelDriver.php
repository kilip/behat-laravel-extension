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

namespace Behat\LaravelExtension\Driver;

use Behat\LaravelExtension\Contracts\LaravelFactoryContract;
use Behat\Mink\Driver\BrowserKitDriver;

class KernelDriver extends BrowserKitDriver
{
    /**
     * KernelDriver constructor.
     *
     * @param LaravelFactoryContract $factory
     * @param string|null            $baseUrl
     */
    public function __construct(LaravelFactoryContract $factory, $baseUrl = null)
    {
        $class = 'Symfony\\Component\\HttpKernel\\Client';
        if (class_exists($test = 'Symfony\\Component\\HttpKernel\\HttpKernelBrowser')) {
            $class = $test;
        }

        $app = $factory->getApplication();

        parent::__construct(new $class($app), $baseUrl);
    }
}

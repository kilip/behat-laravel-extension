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

use Behat\Mink\Driver\BrowserKitDriver;
use Symfony\Component\HttpKernel\HttpKernelInterface;

class KernelDriver extends BrowserKitDriver
{
    /**
     * KernelDriver constructor.
     *
     * @param HttpKernelInterface $app
     * @param string|null         $baseUrl
     */
    public function __construct(HttpKernelInterface $app, $baseUrl = null)
    {
        $class = 'Symfony\\Component\\HttpKernel\\Client';
        if (class_exists($test = 'Symfony\\Component\\HttpKernel\\HttpKernelBrowser')) {
            $class = $test;
        }
        parent::__construct(new $class($app), $baseUrl);
    }
}

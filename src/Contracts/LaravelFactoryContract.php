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

namespace Behat\LaravelExtension\Contracts;

use Illuminate\Foundation\Application;

interface LaravelFactoryContract
{
    /**
     * Boot this factory.
     *
     * @return void
     */
    public function boot();

    /**
     * Shutdown the application.
     *
     * @return void
     */
    public function tearDown();

    /**
     * @return void
     */
    public function reboot();

    /**
     * @return Application
     */
    public function getApplication();
}

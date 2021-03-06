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

namespace Tests\DummyPackage;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DummyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::get('/', DummyAction::class);
    }
}

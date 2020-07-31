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

use Illuminate\Http\Request;
use Illuminate\Routing\Controller;

class DummyAction extends Controller
{
    public function __invoke(Request $request)
    {
        return response("Hello World\n".'Foo value: '.config('foo'));
    }
}

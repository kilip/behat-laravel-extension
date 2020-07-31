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

use Illuminate\Contracts\Foundation\Application as ApplicationContract;

interface ApplicationAwareContract
{
    /**
     * @param ApplicationContract $application
     *
     * @return void
     */
    public function setApplication($application);
}

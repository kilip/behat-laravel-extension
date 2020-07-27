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

namespace Behat\LaravelExtension\Context;

use Behat\LaravelExtension\Contracts\ApplicationAwareContract;
use Illuminate\Foundation\Application;

class LaravelContext implements ApplicationAwareContract
{
    /**
     * @var Application
     */
    private $app;

    /**
     * {@inheritdoc}
     */
    public function setApplication($application)
    {
        $this->app = $application;
    }
}

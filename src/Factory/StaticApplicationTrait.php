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

namespace Behat\LaravelExtension\Factory;

use Illuminate\Foundation\Application;

trait StaticApplicationTrait
{
    public static $theApplication;

    /**
     * @return Application
     */
    abstract public function createApplication();

    /**
     * {@inheritdoc}
     */
    public function boot()
    {
        if (!\is_object(static::$theApplication)) {
            putenv('APP_ENV=testing');
            static::$theApplication = $this->createApplication();
        }

        return;
    }

    /**
     * {@inheritdoc}
     */
    public function tearDown()
    {
        if (\is_object(static::$theApplication)) {
            static::$theApplication->flush();
            static::$theApplication = null;
        }
    }

    /**
     * {@inheritdoc}
     */
    public function __invoke()
    {
        if (!\is_object(static::$theApplication)) {
            $this->boot();
        }

        return static::$theApplication;
    }
}

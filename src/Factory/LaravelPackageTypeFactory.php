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

use Behat\LaravelExtension\Contracts\LaravelFactoryContract;
use Orchestra\Testbench\Concerns\CreatesApplication;

class LaravelPackageTypeFactory implements LaravelFactoryContract
{
    use CreatesApplication;
    use StaticApplicationTrait;

    /**
     * @var array
     */
    private $providers;

    /**
     * @var array
     */
    private $aliases;

    /**
     * @var array
     */
    private $environment;

    public function __construct(
        array $providers,
        array $aliases,
        array $environment
    ) {
        $this->providers = $providers;
        $this->aliases = $aliases;
        $this->environment = $environment;
    }

    protected function getPackageAliases()
    {
        return $this->aliases;
    }

    protected function getPackageProviders()
    {
        return $this->providers;
    }

    protected function getEnvironmentSetUp($app)
    {
        foreach ($this->environment as $key => $value) {
            $app['config']->set($key, $value);
        }
    }
}

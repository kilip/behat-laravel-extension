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
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Contracts\Foundation\Application as ApplicationContract;

class LaravelFactory implements LaravelFactoryContract
{
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
    /**
     * @var string
     */
    private $bootstrapFile;

    /**
     * @var ApplicationContract|null
     */
    private $app;

    /**
     * The callbacks that should be run after the application is created.
     *
     * @var array
     */
    private $afterApplicationCreatedCallbacks = [];

    /**
     * The callbacks that should be run before the application is destroyed.
     *
     * @var array
     */
    private $beforeApplicationDestroyedCallbacks = [];

    /**
     * Indicates if we have made it through the base setUp function.
     *
     * @var bool
     */
    private $setUpHasRun = false;

    private $serverVariables = [];

    /**
     * LaravelFactory constructor.
     *
     * @param string $bootstrapFile
     * @param array  $providers
     * @param array  $aliases
     * @param array  $environment
     */
    public function __construct(
        string $bootstrapFile,
        array $providers = [],
        array $aliases = [],
        array $environment = []
    ) {
        $this->bootstrapFile = $bootstrapFile;
        $this->providers = $providers;
        $this->aliases = $aliases;
        $this->environment = $environment;
    }

    /**
     * Register a callback to be run after the application is created.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function afterApplicationCreated(callable $callback)
    {
        $this->afterApplicationCreatedCallbacks[] = $callback;

        if ($this->setUpHasRun) {
            \call_user_func($callback, $this->app);
        }
    }

    /**
     * Register a callback to be run before the application is destroyed.
     *
     * @param callable $callback
     *
     * @return void
     */
    public function beforeApplicationDestroyed(callable $callback)
    {
        $this->beforeApplicationDestroyedCallbacks[] = $callback;
    }

    public function boot()
    {
        if (!\is_object($this->app)) {
            $this->refreshApplication();
        }

        $this->app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();
        $this->app->make('Illuminate\Http\Request')->capture();

        foreach ($this->afterApplicationCreatedCallbacks as $callback) {
            \call_user_func($callback);
        }

        $this->setUpHasRun = true;
    }

    public function tearDown()
    {
        if ($this->app) {
            foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
                \call_user_func($callback, $this->app);
            }

            $this->app->flush();

            $this->app = null;
        }

        $this->setUpHasRun = false;

        if (property_exists($this, 'serverVariables')) {
            $this->serverVariables = [];
        }

        $this->afterApplicationCreatedCallbacks = [];
        $this->beforeApplicationDestroyedCallbacks = [];
    }

    public function reboot()
    {
        if ($this->app) {
            foreach ($this->beforeApplicationDestroyedCallbacks as $callback) {
                \call_user_func($callback, $this->app);
            }

            $this->app->flush();

            $this->app = null;
        }
        $this->boot();
    }

    /**
     * @throws \Exception
     *
     * @return Application|\Illuminate\Contracts\Foundation\Application
     */
    public function getApplication(): Application
    {
        if (null === $this->app) {
            $this->refreshApplication();
        }

        return $this->app;
    }

    /**
     * Refresh the application instance.
     *
     * @throws \Exception
     *
     * @return void
     */
    private function refreshApplication()
    {
        putenv('APP_ENV=testing');

        $this->app = $this->createApplication();
    }

    /**
     * @throws \Exception
     *
     * @return ApplicationContract
     */
    private function createApplication()
    {
        if (!is_file($this->bootstrapFile)) {
            throw new \Exception('bootstrap file not exists: '.$this->bootstrapFile);
        }
        $app = include $this->bootstrapFile;

        return $app;
    }
}

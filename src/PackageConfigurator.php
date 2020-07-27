<?php

namespace Behat\LaravelExtension;

use Illuminate\Contracts\Foundation\Application;

use Laravel\BrowserKitTesting\Concerns\ImpersonatesUsers;
use Laravel\BrowserKitTesting\Concerns\InteractsWithAuthentication;
use Laravel\BrowserKitTesting\Concerns\InteractsWithConsole;
use Laravel\BrowserKitTesting\Concerns\InteractsWithContainer;
use Laravel\BrowserKitTesting\Concerns\InteractsWithDatabase;
use Laravel\BrowserKitTesting\Concerns\InteractsWithExceptionHandling;
use Laravel\BrowserKitTesting\Concerns\InteractsWithSession;
use Laravel\BrowserKitTesting\Concerns\MakesHttpRequests;
use Laravel\BrowserKitTesting\Concerns\MocksApplicationServices;
use Orchestra\Testbench\Concerns\Testing;

class PackageConfigurator implements ApplicationFactoryInterface
{
    use ImpersonatesUsers,
        InteractsWithAuthentication,
        InteractsWithConsole,
        InteractsWithContainer,
        InteractsWithDatabase,
        InteractsWithExceptionHandling,
        InteractsWithSession,
        MakesHttpRequests,
        MocksApplicationServices,
        Testing;

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
    private $configs;

    public function __construct(
        array $providers = [],
        array $aliases = [],
        array $configs = []
    )
    {
        $this->providers = $providers;
        $this->aliases = $aliases;
        $this->configs = $configs;
    }

    public function boot()
    {
        $this->setUpTheTestEnvironment();
    }

    public function tearDown()
    {
        $this->tearDownTheTestEnvironment();
    }

    public function __invoke()
    {
        return $this->app;
    }

    protected function getEnvironmentSetUp($app)
    {
        foreach($this->configs as $key => $value){
            $app['config']->set($key, $value);
        }
    }

    protected function getPackageAliases($app)
    {
        return $this->aliases;
    }

    protected function getPackageProviders($app)
    {
        return $this->providers;
    }

    /**
     * Boot the testing helper traits.
     *
     * @return array
     */
    protected function setUpTraits()
    {
        $uses = \array_flip(\class_uses_recursive(static::class));

        return $this->setUpTheTestEnvironmentTraits($uses);
    }

    /**
     * Refresh the application instance.
     *
     * @return void
     */
    protected function refreshApplication()
    {
        $_ENV['APP_ENV'] = 'testing';

        $this->app = $this->createApplication();
    }
}

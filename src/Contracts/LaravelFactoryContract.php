<?php


namespace Behat\LaravelExtension\Contracts;


use Illuminate\Foundation\Application;

interface LaravelFactoryContract
{
    /**
     * Boot this factory
     * @return void
     */
    public function boot();

    /**
     * Shutdown the application
     * @return void
     */
    public function tearDown();

    /**
     * @return Application
     */
    public function __invoke();
}
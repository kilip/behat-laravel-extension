<?php


namespace Behat\LaravelExtension;


use Illuminate\Contracts\Foundation\Application;

interface ApplicationFactoryInterface
{
    public function boot();

    /**
     * @return Application|\Illuminate\Foundation\Application
     */
    public function __invoke();
}
<?php


namespace Behat\LaravelExtension\Contracts;

use Illuminate\Foundation\Application;

interface ApplicationAwareContract
{
    /**
     * @param Application $application
     * @return mixed
     */
    public function setApplication($application);
}
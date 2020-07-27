<?php


namespace Behat\LaravelExtension\Context;


use Behat\LaravelExtension\Contracts\ApplicationAwareContract;
use Illuminate\Foundation\Application;
use Laravel\BrowserKitTesting\Concerns\ImpersonatesUsers;
use Laravel\BrowserKitTesting\Concerns\InteractsWithAuthentication;
use Laravel\BrowserKitTesting\Concerns\InteractsWithContainer;

class LaravelContext implements ApplicationAwareContract
{
    use ImpersonatesUsers,
        InteractsWithAuthentication,
        InteractsWithContainer;

    protected $app;

    public function setApplication($application)
    {
        $this->app = $application;
    }

    /**
     * @Given I am logged in as :username
     *
     * @param $username
     */
    public function loggedInAs($username)
    {

    }
}
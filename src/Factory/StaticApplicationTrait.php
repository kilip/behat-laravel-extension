<?php


namespace Behat\LaravelExtension\Factory;


use Illuminate\Foundation\Application;

trait StaticApplicationTrait
{
    static $theApplication;

    /**
     * @return Application
     */
    abstract public function createApplication();

    /**
     * {@inheritDoc}
     */
    public function boot()
    {
        if(!is_object(static::$theApplication)){
            putenv('APP_ENV=testing');
            static::$theApplication = $this->createApplication();
        }
        return;
    }

    /**
     * {@inheritDoc}
     */
    public function tearDown()
    {
        if(is_object(static::$theApplication)){
            static::$theApplication->flush();
            static::$theApplication = null;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function __invoke()
    {
        if(!is_object(static::$theApplication)){
            $this->boot();
        }
        return static::$theApplication;
    }
}
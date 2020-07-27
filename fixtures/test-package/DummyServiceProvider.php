<?php


namespace Tests\DummyPackage;

use Illuminate\Support\Facades\Route;
use Illuminate\Support\ServiceProvider;

class DummyServiceProvider extends ServiceProvider
{
    public function boot()
    {
        Route::get('/',DummyAction::class);
    }
}
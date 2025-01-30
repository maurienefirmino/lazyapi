<?php

namespace maurienejunior\lazyapi;

use Illuminate\Support\ServiceProvider;

class LazyApiServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {

    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__."/views", "lazy-api-views");
        $this->loadRoutesFrom(__DIR__."/routes/web.php");
    }
}

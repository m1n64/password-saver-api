<?php

namespace App\Providers;

use App\Models\Key;
use App\Repositories\KeyModel\KeyInterface;
use Illuminate\Support\ServiceProvider;

class RepositoryServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        switch (env("DB_SEC_CONNECTION")) {
            default:
            case "sqlite":
                $this->app->bind(KeyInterface::class, Key::class);
                break;

            case "pgsql":
                $this->app->bind(KeyInterface::class, \App\Repositories\KeyModel\Key::class);
        }
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }
}

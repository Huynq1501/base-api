<?php

namespace nguyenanhung\Backend\BaseAPI;

use Illuminate\Support\ServiceProvider;

class ServiceProviderForLaravel extends ServiceProvider
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
        $this->publishes([
            __DIR__ . '/../test/config.php' => config_path('base_api.php'),
        ]);
    }
}
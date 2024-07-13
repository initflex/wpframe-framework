<?php

namespace Wpframe\Sys;

use Wpframe\Sys\Logger\Log;
use Wpframe\Sys\Support\View;
use Wpframe\Sys\Support\Session;
use Wpframe\Sys\Routing\Controller;
use Wpframe\Sys\Support\ServiceProvider;
use Wpframe\Sys\Database\DatabaseManager;

class SystemServiceProvider extends ServiceProvider
{
    /**
     * Load First
     *
     * @return void
     */
    public function register()
    {
        Log::start()->logger(wpf_env('APP_ENV', 'wpframe'))->pushHandler();
        
        $this->container->singleton(View::class);
        $this->container->singleton(Session::class)
            ->make(Session::class)
            ->start(wpf_config('session'));
        
        $this->container->singleton(DatabaseManager::class)
            ->make(DatabaseManager::class)
            ->start(wpf_config('database'));

        $this->container->singleton(Controller::class);
    }
}
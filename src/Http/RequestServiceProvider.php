<?php

namespace Wpframe\Sys\Http;

use Wpframe\Sys\Http\Request;
use Wpframe\Sys\Routing\Route;
use Wpframe\App\Http\Controllers\Controller;
use Wpframe\Sys\Routing\MenuRoute;
use Wpframe\Sys\Support\ServiceProvider;

class RequestServiceProvider extends ServiceProvider
{
    /**
     * Load First
     *
     * @return void
     */
    public function register()
    {
        require_once wpf_system_path('/Traits/Menu/AdminMenu.php');

        $this->container->singleton(Request::class)
            ->make(Request::class)
            ->start();
        $request = $this->container->make(Request::class);
        $request->requestHandler();

        $this->container->singleton(Controller::class, function () {
            return;
        });
        
        $this->container->singleton(MenuRoute::class);
        $this->container->singleton(Route::class);
    }
}

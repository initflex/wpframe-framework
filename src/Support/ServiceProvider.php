<?php

namespace Wpframe\Sys\Support;

use Wpframe\Sys\Container\Container;

/**
 * binding components or services into containers
 */
class ServiceProvider
{
    public static $providers = [];
    public static $providerFiles = [];
    public static $providersClass = [];
    public static $services = [];
    /**
     * Container instantiation
     *
     * @return \Wpframe\Sys\Container\Container
     */
    public $container;

    /**
     * Initializes the Container
     * 
     * @return void
     */
    public function __construct()
    {
        $this->container = Container::start();
    }

    /**
     * Initialize and Start Service Provider
     *
     * @param array $providers
     * @return \Wpframe\Sys\Support\ServiceProvider
     */
    public static function start($providers = [])
    {
        self::$providers = $providers;
        foreach ($providers as $provider) {
            $providerFile = '/' . wpf_class_dir($provider) . '.php';
            self::$providerFiles[] = $providerFile;
            if (file_exists(wpf_base_path($providerFile))) {
                require wpf_base_path($providerFile);
            }
        }
        self::exRegister($providers);
        return new static;
    }

    /**
     * Initialize the register method in the service provider
     *
     * @param array $providerClass
     * @return \Wpframe\Sys\Support\ServiceProvider
     */
    public static function exRegister($providerClass = [])
    {
        foreach ($providerClass as $provider) {
            if (class_exists($provider)) {
                self::$providersClass[$provider] = new $provider();
                if(method_exists($provider, 'register')) {
                    self::$providersClass[$provider]->register();
                }
            }
        }
        return new static;
    }

    /**
     * Initialize the boot method in the service provider
     *
     * @param array $providerClass
     * @return \Wpframe\Sys\Support\ServiceProvider
     */
    public static function exBoot($providerClass = [])
    {
        foreach ($providerClass as $provider) {
            if (class_exists($provider)) {
                if(method_exists($provider, 'boot')) {
                    self::$providersClass[$provider]->boot();
                }
            }
        }
        return new static;
    }
}

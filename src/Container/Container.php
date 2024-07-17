<?php

namespace Wpframe\Sys\Container;

use Closure;
use Wpframe\Sys\Support\ClassSupport;

/**
 * Container for wpframe
 */
class Container
{
    /**
     * Dependencies
     *
     * @var array
     */
    public static $dependencies = [];

    /**
     * Start the container
     *
     * @return \Wpframe\Sys\Container\Container
     */
    public static function start()
    {
        return new static;
    }

    /**
     * Hook the class, or whatever it is to the container dependencies
     *
     * @param string $identifier
     * @param closure|null $callback
     * @return \Wpframe\Sys\Container\Container
     */
    public static function singleton($identifier, $callback = null)
    {
        // set prefix path
        $prefixPath = '/';
        // check the system's namespace and set prefix path
        if (strpos($identifier, 'Wpframe\Sys') !== false) {
            $prefixPath = '/vendor/initflex/wpframe-framework/src/';
        }
        // set class file
        $classFile = $prefixPath . str_replace('App/', 'app/', wpf_class_dir($identifier)) . '.php';

        if (!class_exists($identifier)) {
            if (file_exists(wpf_base_path($classFile))) {
                require wpf_base_path($classFile);
            }
        }

        if ($callback == null) {
            self::$dependencies[$identifier] = null;
        }

        if ($callback instanceof Closure) {
            self::$dependencies[$identifier] = $callback();
        }

        if (class_exists($identifier) && $callback == null) {
            self::$dependencies[$identifier] = new $identifier();
        }

        if (!class_exists($identifier) && $callback !== null) {
            self::$dependencies[$identifier] = $callback();
        }

        return new static;
    }

    /**
     * Create alternative names or synonyms for dependencies
     *
     * @param string $identifier
     * @param string $aliasName
     * @return \Wpframe\Sys\Container\Container
     */
    public function alias($identifier, $aliasName = null)
    {
        if (isset(self::$dependencies[$identifier])) {
            self::$dependencies[$aliasName] = self::$dependencies[$identifier];
        }
        return $this;
    }

    /**
     * Resolve ::make() with parameters
     *
     * @param string $identifier
     * @param array $parameters
     * @return mixed
     */
    public static function resolveMakeWithParams($identifier, $parameters = [])
    {
        if (class_exists($identifier)) {
            return new $identifier(...array_values($parameters));
        }

        if (self::$dependencies[$identifier] instanceof Closure) {
            return self::$dependencies[$identifier](...array_values($parameters));
        }

        return false;
    }

    /**
     * Resolve and get dependency
     *
     * @param string $identifier
     * @param array $parameters
     * @return mixed
     */
    public static function make($identifier, $parameters = [])
    {
        if (isset(self::$dependencies[$identifier])) {
            if (count($parameters) > 0) {
                return self::resolveMakeWithParams($identifier, $parameters);
            }
            return self::$dependencies[$identifier];
        }
        return false;
    }

    /**
     * Get all dependencies
     *
     * @return array
     */
    public function getAllDependencies()
    {
        return self::$dependencies;
    }

    /**
     * Call Class and return instance of Class
     *
     * @param string|closure $class
     * @param mixed $method
     * @param array $data
     * @return mixed
     */
    public static function callAction($class, $method = null, $data = [])
    {
        if(!$class instanceof Closure) {
            self::singleton($class, function() {
                return;
            });
        }

        $resolveParamsClass = wpf_app(ClassSupport::class);
        $paramsConstructClosure = $resolveParamsClass->getDependenciesClassArr(
            $resolveParamsClass->getParamsClass($class), $data
        );

        if(!$class instanceof Closure) {
            $parametersMethod = $resolveParamsClass->getDependenciesClassArr(
                $resolveParamsClass->getParamsClass($class, $method), $data
            );
            return (new $class(...array_values($paramsConstructClosure)))
            ->{$method}(...array_values($parametersMethod));
        }
        return $class(...array_values($paramsConstructClosure));
    }
}
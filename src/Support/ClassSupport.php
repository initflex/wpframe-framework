<?php

namespace Wpframe\Sys\Support;

use Closure;
use ReflectionClass;
use ReflectionFunction;
use ReflectionMethod;
use Wpframe\Bootstrap\App;

/**
 * Support class for class manipulation
 */
class ClassSupport
{
    /**
     * Get Parameters from a class or closure
     *
     * @param string $class
     * @param string $method
     * @return mixed|\ReflectionParameter[]
     */
    public function getParamsClass($class = null, $method = null)
    {
        if (!$class instanceof Closure && $method == null) {
            $checkMethodParam = new ReflectionClass($class);
            $set = $checkMethodParam->getConstructor();
        } else if ($class instanceof Closure && $method == null) {
            $set = new ReflectionFunction($class);
        } else {
            $set = new ReflectionMethod($class, $method);
        }
        return $set->getParameters();
    }

    /**
     * Get dependencies from class parameters or closures
     *
     * @param array $params
     * @param array $secondaryDependencies
     * @return array|mixed
     */
    public function getDependenciesClassArr($params = [], $secondaryDependencies = [])
    {
        $items = [];
        foreach ($params as $param) {
            $type = $param->getType();
            $nonClassClosure = isset(App::$app['dependencies'][$param->getName()]) ?
                App::$app['dependencies'][$param->getName()] : (isset($secondaryDependencies[$param->getName()]) ?
                    $secondaryDependencies[$param->getName()] : ''
                );

            if ($type && !$type->isBuiltin()) {
                $typeName = $type->getName();
                if (!$typeName instanceof Closure) {
                    $reflectionClass = new ReflectionClass($typeName);
                } else {
                    $reflectionClass = new ReflectionFunction($typeName);
                }

                $className = $reflectionClass->getName();
                $items[] = isset(App::$app['dependencies'][$className]) ?
                    App::$app['dependencies'][$className] : $nonClassClosure;
                continue;
            }

            if ($type !== null && $type->getName() !== null) {
                $className = $type->getName();
                $items[] = isset(App::$app['dependencies'][$className]) ?
                    App::$app['dependencies'][$className] : $nonClassClosure;
            }
        }
        return $items;
    }
}

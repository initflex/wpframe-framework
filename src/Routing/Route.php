<?php

namespace Wpframe\Sys\Routing;

use Wpframe\Sys\Http\Request;
use Wpframe\Sys\Routing\Controller;

/**
 * Routing for the frontend on the application
 */
class Route
{
    private static $routeSet = false;
    private static $routeCallback = false;
    private static $setMethod = false;
    private static $routesData = [];
    private static $routeSetPrefix = false;
    private static $routePosition = 0;
    private static $requestMethodSet = false;
    private $routeParamValidate = false;
    private $middlewareName = false;

    /**
     * Register route files
     *
     * @param string $routeFile
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function routeRegister($routeFile = null)
    {
        if ($routeFile == null || !file_exists($routeFile)) {
            return false;
        }
        require_once $routeFile;
        return new static;
    }

    /**
     * Assign the value to the specified route
     *
     * @param mixed $setMethod
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @param bool|string $requestMethodSet
     * @return void
     */
    private static function setMethodValues($setMethod = false, $routeSet = false, $routeCallback = false, $requestMethodSet = false)
    {
        self::$setMethod = $setMethod;
        self::$routeSet = $routeSet;
        self::$routeCallback = $routeCallback;
        self::$routePosition = count(self::$routesData);
        self::$routesData[] = [
            'route_prefix'      =>  self::$routeSetPrefix,
            'method'            =>  self::$setMethod,
            'validate_method'   =>  $requestMethodSet,
            'route'             =>  self::$routeSet,
            'callback'          =>  self::$routeCallback
        ];
    }

    /**
     * The specified request type is "get"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function get($routeSet = false, $routeCallback = false)
    {
        self::$requestMethodSet = wpf_app(Request::class)->isGet() ? 'GET' : false;
        self::setMethodValues('GET', $routeSet, $routeCallback, self::$requestMethodSet);
        return new static;
    }

    /**
     * The specified request type is "post"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function post($routeSet = false, $routeCallback = false)
    {
        self::$requestMethodSet = wpf_app(Request::class)->isPost() ? 'POST' : false;
        self::setMethodValues('POST', $routeSet, $routeCallback, self::$requestMethodSet);
        return new static;
    }

    /**
     * The specified request type is "put"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function put($routeSet = false, $routeCallback = false)
    {
        self::$requestMethodSet = wpf_app(Request::class)->isPut() ? 'PUT' : false;
        self::setMethodValues('PUT', $routeSet, $routeCallback, self::$requestMethodSet);
        return new static;
    }

    /**
     * The specified request type is "delete"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function delete($routeSet = false, $routeCallback = false)
    {
        self::$requestMethodSet = wpf_app(Request::class)->isDelete() ? 'DELETE' : false;
        self::setMethodValues('DELETE', $routeSet, $routeCallback, self::$requestMethodSet);
        return new static;
    }

    /**
     * The specified request type is "patch"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function patch($routeSet = false, $routeCallback = false)
    {
        self::$requestMethodSet = wpf_app(Request::class)->isPatch() ? 'PATCH' : false;
        self::setMethodValues('PATCH', $routeSet, $routeCallback, self::$requestMethodSet);
        return new static;
    }

    /**
     * The specified request type is "options"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function options($routeSet = false, $routeCallback = false)
    {
        self::$requestMethodSet = wpf_app(Request::class)->isOptions() ? 'OPTIONS' : false;
        self::setMethodValues('OPTIONS', $routeSet, $routeCallback, self::$requestMethodSet);
        return new static;
    }

    /**
     * The specified request type is "any"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function any($routeSet = false, $routeCallback = false)
    {
        self::$requestMethodSet = 'ANY';
        self::setMethodValues('ANY', $routeSet, $routeCallback, self::$requestMethodSet);
        return new static;
    }

    /**
     * The specified request type is "match"
     *
     * @param string $routeSet
     * @param array|closure $routeCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function match($routeSet = false, $routeCallback = false)
    {
        self::$setMethod = 'MATCH';
        self::$routeSet = $routeSet;
        self::$routeCallback = $routeCallback;
        self::$routePosition = count(self::$routesData);
        self::$routesData[] = [
            'route_prefix'      =>  self::$routeSetPrefix,
            'method'            =>  self::$setMethod,
            'route'             =>  self::$routeSet,
            'callback'          =>  self::$routeCallback
        ];

        return new static;
    }

    /**
     * Matches the method of the route
     *
     * @param array $matchMethod
     * @return \Wpframe\Sys\Routing\Route
     */
    public function methodMatch($matchMethod = []) 
    {   
        if (self::$requestMethodSet ) return $this;
        if (!is_array($matchMethod) ) return $this;
        if (count($matchMethod) == 0 ) return $this;
        self::$requestMethodSet = wpf_app(Request::class)->isMatch($matchMethod) ? 'MATCH' : false;
        self::$routesData[self::$routePosition]['validate_method'] = self::$requestMethodSet;
        return $this;
    }

    /**
     * Create a prefix in the url route
     *
     * @param string $routeSetPrefix
     * @return \Wpframe\Sys\Routing\Route
     */
    public static function prefix($routeSetPrefix = false)
    {
        self::$routeSetPrefix = $routeSetPrefix;
        return new static;
    }

    /**
     * Create groups on routes
     *
     * @param boolean $groupCallback
     * @return \Wpframe\Sys\Routing\Route
     */
    public function group($groupCallback = false)
    {
        if (!$groupCallback) return $this;
        $groupCallback();
        return $this;
    }

    /**
     * ========== This features in progress
     *
     * @param string $middlewareName
     * @return \Wpframe\Sys\Routing\Route
     */
    public function middleware($middlewareName = false)
    {
        $this->middlewareName = $middlewareName;
        return $this;
    }

    /**
     * Pattern to validate variables on a route
     *
     * @param array $routeParamValidate
     * @return \Wpframe\Sys\Routing\Route
     */
    public function where($routeParamValidate = false)
    {
        $this->routeParamValidate = $routeParamValidate;
        self::$routesData[self::$routePosition] = array_merge(
            self::$routesData[self::$routePosition],
            ['route_param_validate' =>  $this->routeParamValidate]
        );
        return $this;
    }

    /**
     * Validate variables in route url
     *
     * @param mixed $routeUrl
     * @param mixed $urlAppPath
     * @return bool|array
     */
    private static function validateRouteUrl($routeUrl = false, $urlAppPath = false)
    {
        $getSlugArray = $urlAppPath == '/' ? [] : explode('/', $urlAppPath);
        if (isset($getSlugArray[0]) && $getSlugArray[0] == '') {
            unset($getSlugArray[0]);
        }
        $getSlugArray = array_values($getSlugArray);
        $fullUrlRoute = isset($routeUrl['route_prefix']) && $routeUrl['route_prefix'] ? 
            $routeUrl['route_prefix'] . $routeUrl['route'] : $routeUrl['route'];

        $getRouteArray = $fullUrlRoute == '/' ? [] : explode('/', $fullUrlRoute);
        if (isset($getRouteArray[0]) && $getRouteArray[0] == '') {
            unset($getRouteArray[0]);
        }
        $getRouteArray = array_values($getRouteArray);

        // validate if route and client request url are the same array count.
        if(count($getRouteArray) == count($getSlugArray)) {

            // validate per path url
            $x = 0;
            $routeUrlValidateStatus = [];
            $routeRuleSetVar = [];
            foreach ($getSlugArray as $slugPathItem) {
                if(preg_match('/^\{.*\}$/', $getRouteArray[$x])) {
                    $getRouteRuleName = str_replace(['{', '}'], ['', ''], $getRouteArray[$x]);
                    $routeRuleSet = $routeUrl['route_param_validate'][$getRouteRuleName];
                    $patternSetRoute = "/^$routeRuleSet$/";
                    $validatePathRule = preg_match($patternSetRoute, $slugPathItem);
                    $routeUrlValidateStatus[] = $validatePathRule ? true : false;
                    $routeRuleSetVar = array_merge($routeRuleSetVar, [$getRouteRuleName =>  $slugPathItem]);
                    $x++;
                    continue;
                }

                if($getRouteArray[$x] == $slugPathItem && !preg_match('/^\{.*\}$/', $getRouteArray[$x])) {
                    $routeUrlValidateStatus[] = true;
                } else {
                    $routeUrlValidateStatus[] = false;
                }
                $x++;
            }

            $setData = [
                'route_url' =>  $fullUrlRoute,
                'rules_values'  => $routeRuleSetVar,
            ];

            if (in_array(false, $routeUrlValidateStatus)) return false; 
            return $setData;
        }
    }

    /**
     * Run route
     *
     * @param array $appConfig
     * @return void
     */
    public static function run($appConfig = [])
    {
        $request = wpf_app(Request::class);
        $controller = wpf_app(Controller::class);
        $urlAppPath = $request::urlAppPath(wpf_get_domain_url() . $request::urlPathInfo(), $appConfig['url']);
        
        foreach (self::$routesData as $routeItem) {
            $validateRouteUrl = self::validateRouteUrl($routeItem, $urlAppPath);
            if ($validateRouteUrl && isset($routeItem['validate_method']) && $routeItem['validate_method']) {
                if (is_array($routeItem['callback'])) {
                    $controller->dispatcher(
                        $routeItem['callback'][0], 
                        $routeItem['callback'][1], 
                        $validateRouteUrl['rules_values']
                    );
                    break;
                }

                $controller->dispatcher(
                    $routeItem['callback'], 
                    null, 
                    $validateRouteUrl['rules_values']
                );
                break;
            }
        }
    }
}
<?php

namespace Wpframe\Sys\Routing;

use Wpframe\Sys\Http\Request;
use Wpframe\Sys\Container\Container;
use Wpframe\Sys\Traits\Menu\AdminMenu;

/**
 * Register backend menu and routing
 */
class MenuRoute
{
    use AdminMenu;

    // set default values for Left Menu
    private static $slug = false;
    private static $callback = false;
    private static $menuName = false;
    private $pageTitle = false;
    private $capability = 'manage_options';
    private $icon = '';
    private $position = 1;

    // set default values for Left Sub Menu
    private static $subMenuName = false;
    private static $subSlug = false;
    private static $subCallback = false;
    private static $submenuIndicator = false;
    private $submenuData = false;
    private $subPageTitle = false;
    private $subCapability = 'manage_options';
    private $subPosition = 1;

    // set default values for any request Type
    private static $requestTypeIndicator = false;
    private static $requestSet = false;
    private static $callbackMethod = false;
    private static $setSlugMethod = false;
    private static $methodSet = false;
    private $matchMethod = [];

    /**
     * Add parent menu
     *
     * @param mixed $slug
     * @param array|closure $callback
     * @param mixed $menuName
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function add($slug = false, $callback = false, $menuName = false)
    {
        self::$slug = $slug;
        self::$callback = $callback;
        self::$menuName = $menuName;
        return new static;
    }

    /**
     * Add sub menu
     *
     * @param mixed $subSlug
     * @param array|closure $subCallback
     * @param mixed $subMenuName
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function addSub($subSlug = false, $subCallback = false, $subMenuName = false)
    {
        self::$subSlug = $subSlug;
        self::$subCallback = $subCallback;
        self::$subMenuName = $subMenuName;
        self::$submenuIndicator = true;
        return new static;
    }

    /**
     * Set the value for the set method
     *
     * @param array ...$config
     * @return void
     */
    private static function setRouteConfig(...$config): void
    {
        self::$methodSet = $config[0];
        self::$requestTypeIndicator = true;
        self::$setSlugMethod = $config[1];
        self::$callbackMethod = $config[2];
    }

    /**
     * The specified request type is "get"
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function get($setSlug = false, $callback = false, $methodSet = false)
    {
        self::setRouteConfig($methodSet, $setSlug, $callback);
        self::$requestSet = wpf_app(Request::class)->isGet() ? 'GET' : false;
        return new static;
    }

    /**
     * The specified request type is "post"
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function post($setSlug = false, $callback = false, $methodSet = false)
    {
        self::setRouteConfig($methodSet, $setSlug, $callback);
        self::$requestSet = wpf_app(Request::class)->isPost() ? 'POST' : false;
        return new static;
    }

    /**
     * The specified request type is "put"
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function put($setSlug = false, $callback = false, $methodSet = false)
    {
        self::setRouteConfig($methodSet, $setSlug, $callback);
        self::$requestSet = wpf_app(Request::class)->isPut() ? 'PUT' : false;
        return new static;
    }

    /**
     * The specified request type is "delete"
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function delete($setSlug = false, $callback = false, $methodSet = false)
    {
        self::setRouteConfig($methodSet, $setSlug, $callback);
        self::$requestSet = wpf_app(Request::class)->isDelete() ? 'DELETE' : false;
        return new static;
    }

    /**
     * The specified request type is "patch"
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function patch($setSlug = false, $callback = false, $methodSet = false)
    {
        self::setRouteConfig($methodSet, $setSlug, $callback);
        self::$requestSet = wpf_app(Request::class)->isPatch() ? 'PATCH' : false;
        return new static;
    }

    /**
     * The specified request type is "options"
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function options($setSlug = false, $callback = false, $methodSet = false)
    {
        self::setRouteConfig($methodSet, $setSlug, $callback);
        self::$requestSet = wpf_app(Request::class)->isOptions() ? 'OPTIONS' : false;
        return new static;
    }

    /**
     * To handle all types of requests
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function any($setSlug = false, $callback = false, $methodSet = false)
    {
        self::setRouteConfig($methodSet, $setSlug, $callback);
        self::$requestSet = 'ANY';
        return new static;
    }

    /**
     * The specified request type is "match"
     *
     * @param array $setSlug
     * @param array|closure $callback
     * @param mixed $methodSet
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public static function match($setSlug = false, $callback = false, $methodSet = false)
    {
        self::$requestSet = false;
        self::setRouteConfig($methodSet, $setSlug, $callback);
        return new static;
    }

    /**
     * matching method
     *
     * @param array $matchMethod
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public function methodMatch($matchMethod = [])
    {
        if (self::$requestSet) return $this;
        if (!is_array($matchMethod)) return $this;
        if (count($matchMethod) == 0) return $this;
        $this->matchMethod = $matchMethod;
        self::$requestSet = wpf_app(Request::class)->isMatch($this->matchMethod) ? 'MATCH' : false;
        return $this;
    }

    /**
     * Set menu icon
     *
     * @param string $icon
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public function icon($icon)
    {
        $this->icon = $icon;
        return $this;
    }

    /**
     * Set menu position
     *
     * @param string $position
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public function position($position)
    {
        if (self::$submenuIndicator) {
            $this->subPosition = $position;
        } else {
            $this->position = $position;
        }
        return $this;
    }

    /**
     * Set menu capability
     *
     * @param string $capability
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public function capability($capability)
    {
        if (self::$submenuIndicator) {
            $this->subCapability = $capability;
        } else {
            $this->capability = $capability;
        }
        return $this;
    }

    /**
     * Set menu title
     *
     * @param string $title
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public function title($title)
    {
        if (self::$submenuIndicator) {
            $this->subPageTitle = $title;
        } else {
            $this->pageTitle = $title;
        }
        return $this;
    }

    /**
     * Execute to register sub menu item
     *
     * @return array
     */
    public function sub()
    {
        self::$submenuIndicator = false;
        return [
            'subSlug'   => self::$subSlug,
            'subCallback' => self::$subCallback,
            'subMenuName' => self::$subMenuName,
            'data'  => $this,
        ];
    }

    /**
     * List of sub menus
     *
     * @param array $submenu
     * @return \Wpframe\Sys\Routing\MenuRoute
     */
    public function submenu($submenu)
    {
        $this->submenuData = $submenu;
        return $this;
    }

    /**
     * Load and register the controller to the container
     *
     * @param mixed $callback
     * @param string $page
     * @param string $slug
     * @return bool
     */
    private function controllerLoader($callback = false, $page = false, $slug = false): bool
    {
        if ($callback == false && !is_array($callback) && !$page && !$slug) {
            return false;
        }

        if (is_array($callback) && isset($callback[0]) && $page !== false && $page == $slug) {
            Container::singleton($callback[0], function () {
                return;
            });
            return true;
        }
        return false;
    }

    /**
     * Add Parent Menu by Finder
     * 
     * @return void
     */
    private function exParentMenuByFinder($dataSet = [])
    {
        global $menu;

        $menuData = $this->getMenuBySlug($menu, $dataSet['set_slug_method']);
        $this->controllerLoader($dataSet['callback'], '', '');

        add_menu_page(
            $menuData['menu_data'][0], // page_title
            $menuData['menu_data'][3], // menu_title
            $menuData['menu_data'][1], // capability
            $menuData['menu_data'][2], // menu_slug
            (is_array($dataSet['callback']) ?
                [new $dataSet['callback'][0], $dataSet['callback'][1]] :
                $dataSet['callback']
            ),
            $menuData['menu_data'][6], // icon_url
            $menuData['menu_position'], // position
        );
    }

    /**
     * Add Sub Menu by Finder
     * 
     * @return void
     */
    private function exSubMenuByFinder($dataSet = [])
    {
        global $submenu;

        $menuData = $this->getSubmenuBySlug($submenu, $dataSet['set_slug_route']);
        remove_submenu_page($dataSet['set_slug_route'][0], $dataSet['set_slug_route'][1]);
        $this->controllerLoader($dataSet['callback'], '', '');

        add_submenu_page(
            $dataSet['set_slug_route'][0], // parent_slug
            $menuData['submenu_data'][3], // page_title
            $menuData['submenu_data'][0], // menu_title
            $menuData['submenu_data'][1], // capability
            $dataSet['set_slug_route'][1], // menu_slug
            (is_array($dataSet['callback']) ?
                [new $dataSet['callback'][0], $dataSet['callback'][1]] :
                $dataSet['callback']
            ),
            $menuData['submenu_position'], // position
        );
    }

    /**
     * If the request has a specific method and has been 
     * defined in the route.
     *
     * @return void
     */
    private function executeRequestType()
    {
        $slugIsParent = !is_array(self::$setSlugMethod) ? true : false;
        $dataSet = [
            'slug_menu' => (isset($_GET['page']) ? $_GET['page'] : false),
            'slug_method' => (isset($_GET['m']) ? $_GET['m'] : false),
            'slug_is_parent' => $slugIsParent,
            'set_slug_route' => self::$setSlugMethod,
            'set_slug_method' => ($slugIsParent ? self::$setSlugMethod : self::$setSlugMethod[1]),
            'callback' => self::$callbackMethod,
            'method_set' => self::$methodSet,
        ];

        if (
            $dataSet['set_slug_method'] == $dataSet['slug_menu'] &&
            self::$requestSet !== false && $dataSet['slug_method'] == $dataSet['method_set']
        ) {
            if ($slugIsParent) {
                add_action('admin_init', function () use ($dataSet) {
                    remove_menu_page($dataSet['set_slug_method']);
                });

                add_action('admin_menu', function () use ($dataSet) {
                    $this->exParentMenuByFinder($dataSet);
                }, 9999);
            } else {
                add_action('admin_menu', function () use ($dataSet) {
                    $this->exSubMenuByFinder($dataSet);
                }, 9999);
            }
        }
    }

    /**
     * Add Parent Menu
     * 
     * @return void
     */
    private function exParentMenu($dataSet = [])
    {
        add_menu_page(
            ($this->pageTitle ? $this->pageTitle : $dataSet['menu_name']),
            $dataSet['menu_name'],
            $this->capability,
            $dataSet['slug'],
            ($dataSet['page'] !== false && $dataSet['page'] == $dataSet['slug'] ?
                (is_array($dataSet['callback']) ?
                    [new $dataSet['callback'][0], $dataSet['callback'][1]] :
                    $dataSet['callback']
                ) :
                function () {
                    return;
                }
            ),
            $this->icon,
            $this->position,
        );
    }

    /**
     * Add Sub Menu
     * 
     * @return void
     */
    private function exSubMenu($dataSet = [])
    {
        if ($dataSet['submenu']) {
            foreach ($dataSet['submenu'] as $submenuItem) {
                $this->controllerLoader($submenuItem['subCallback'], $dataSet['page'], $submenuItem['subSlug']);
                add_submenu_page(
                    $dataSet['slug'],
                    ($submenuItem['data']->subPageTitle ? $submenuItem['data']->subPageTitle : $submenuItem['subMenuName']),
                    $submenuItem['subMenuName'],
                    $submenuItem['data']->subCapability,
                    $submenuItem['subSlug'],
                    ($dataSet['page'] !== false && $dataSet['page'] == $submenuItem['subSlug'] ?
                        (is_array($submenuItem['subCallback']) ?
                            [new $submenuItem['subCallback'][0], $submenuItem['subCallback'][1]] :
                            $submenuItem['subCallback']
                        ) :
                        function () {
                            return;
                        }
                    ),
                    $submenuItem['data']->subPosition,
                );
            }
        }
    }

    /**
     * Run Route List and Menu
     * 
     * @return void
     */
    public function run()
    {
        if (self::$requestTypeIndicator) {
            /**
             * If the request has a specific method and has been 
             * defined in the route.
             */
            $this->executeRequestType();
        } else {
            /**
             * If the request is a set menu or submenu that 
             * has been specified on the route
             */
            $dataSet = [
                'slug' => self::$slug,
                'menu_name' => self::$menuName,
                'callback' => self::$callback,
                'submenu' => $this->submenuData,
                'page' => (isset($_GET['page']) ? $_GET['page'] : false),
            ];

            $this->controllerLoader($dataSet['callback'], $dataSet['page'], $dataSet['slug']);
            add_action('admin_menu', function () use ($dataSet) {
                $this->exParentMenu($dataSet);
                $this->exSubMenu($dataSet);
            }, 0);
        }
    }
}

<?php

namespace Wpframe\Sys\Traits\Menu;

/**
 * Additional functionality for Admin Menu management
 */
trait AdminMenu 
{   
    /**
     * Get parent menu item by slug name
     *
     * @param array $menuList
     * @param string $slug
     * @return boolean|array
     */
    public function getMenuBySlug($menuList = [], $slug = ''): bool|array
    {
        $data = false;
        foreach ($menuList as $menuItem => $menuVal) {
            if (isset($menuVal[2]) && $menuVal[2] == $slug) {
                $menuPosition = $menuItem;
                $data = [
                    'menu_position' => $menuPosition,
                    'menu_data' => $menuVal,
                ];
                break;
            }
        }
        return $data;
    }
    
    /**
     * Get sub menu item by slug name
     *
     * @param array $submenuList
     * @param array $slug
     * @return boolean|array
     */
    public function getSubmenuBySlug($submenuList = [], $slug = []): bool|array
    {
        $data = false;
        if (!is_array($slug) ) return false;
        foreach ($submenuList as $menuItem => $menuVal) {
            if (isset($slug[0]) && isset($slug[1]) && isset($submenuList[$slug[0]])) {
                foreach ($submenuList[$slug[0]] as $submenuItem => $submenuVal) {
                    if (isset($submenuVal[2]) && $submenuVal[2] == $slug[1]) {
                        $submenuPosition = $menuItem;
                        $data = [
                            'menu_parent'   =>  $slug[0],
                            'submenu_position' => $submenuItem,
                            'submenu_data' => $submenuVal,
                        ];
                        break;
                    }
                }
            }
        }
        return $data;
    }
}
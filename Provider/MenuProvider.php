<?php

namespace Btn\MenuBundle\Provider;

/**
 * Menu service provider
 * Read menu from config file - parse it to array
 * and mark active item based on the routing
 *
 * @package btn.menu
 * @author  lroth
 **/
class MenuProvider
{
    /**
     * Menu array
     *
     * @var array $menu
     **/
    private $menu;

    /**
     * Service container
     * Dummy way to get the request here without scope request in service def
     * @TODO: think about this one more time
     *
     * @var ServiceContainer $container
     **/
    private $container;

    /**
     * Current route
     *
     * @var string $currentRoute
     **/
    private $currentRoute;

    /**
     * User roles - optional
     *
     * @var array $userRoles
     **/
    private $userRoles;

    /**
     * Constructor injection
     *
     * @param  array $menu
     * @param  ServiceContainer $container
     * @return void
     **/
    public function __construct($menu)
    {
        //menu injected from config file
        $this->menu         = $menu;
    }

    /**
     * Basic menu retriever
     *
     * @return array $menu
     **/
    public function retrieve($route = '', $level = null, $roles = null)
    {
        //if current route is not set
        if (!$this->currentRoute) {
            $this->currentRoute = $route;
        }

        //if some user roles are passed
        $this->userRoles = $roles;

        //mark some items as active based on the current route name
        $this->markActive($this->menu);

        //show only one specific level for active main item
        if ($level) {
            //if we don't have parent here - throw empty submenu
            return $this->currentRoute ? $this->sliceMenu($level) : array();
        }

        //return whole menu
        return $this->menu;
    }

    /**
     * Set current route name to be marked as active
     * settable from ie. controller
     *
     * @param  string $route
     * @return boid
     **/
    public function setCurrentRoute($route = '')
    {
        $this->currentRoute = $route;
    }

    /**
     * OMG Recursion!
     * Take reference to the items from $this->menu and check for each item
     * if has route equal to the current routing from request, if so mark as active + mark parent
     * and call it for all childrens.
     *
     * @return void
     **/
    private function markActive(&$childrens, &$parents = array()) {
        //for each children
        foreach ($childrens as $key => &$item) {

            //check roles
            if (isset($item['role']) && is_array($this->userRoles) && !in_array($item['role'], $this->userRoles)) {
                // ldd($item);
                unset($childrens[$key]);
                // ld($childrens[$key]);
            }

            //set current level
            $item['level']  = count($parents) + 1;
            //mark item as inactive by default
            $item['active'] = false;

            //set child and parents as active if current route match
            if (isset($item['route']) && $item['route'] === $this->currentRoute ) {

                //mark item as active
                $item['active'] = true;
                //check for stored parents
                if (count($parents)) {
                    //and mark them as active
                    foreach ($parents as &$parent) {
                        $parent['active'] = true;
                    }
                }
            }
            //if we have some childrens for this item
            if (isset($item['childrens']) && count($item['childrens']) > 0) {
                //play with parents

                //store current item in parents array
                $parents[] = &$item;

                //call self for childrens and with current parents
                $this->markActive($item['childrens'], $parents);
            }
        }

        //reset parents for all first level items
        $parents = array();
    }

    /**
     * Slice menu to specific level for main active menu item
     *
     * @param integer $level
     * @return array $menu
     **/
    private function sliceMenu($level)
    {
        foreach ($this->menu as $item) {
            //for current route find childrens
            if (isset($item['active']) && $item['active'] === true) {
                //if has childrens - return them
                if (isset($item['childrens']) && count($item['childrens']) > 0) {
                    return $item['childrens'];
                }
            }
        }

        //return empty
        return array();
    }
}

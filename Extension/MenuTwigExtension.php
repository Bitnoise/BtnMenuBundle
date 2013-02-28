<?php

namespace Btn\MenuBundle\Extension;

/**
 * Menu service extension for twig
 *
 * @package btn.menu
 * @author  lroth
 **/
class MenuTwigExtension extends \Twig_Extension
{
    /**
     * Menu array
     *
     * @var array $menu
     **/
    private $menu;

    /**
     * Constructor injection
     *
     * @param  MenuProvider $container
     * @return void
     **/
    public function __construct($menu)
    {
        $this->menu         = $menu;
    }

    public function getFunctions() {
        return array(
            'btn_menu' => new \Twig_Function_Method($this, 'retrieve'),
        );
    }

    public function retrieve($route = '', $level = null, $userRoles = null) {

        //retrieve menu from service
        $menu = $this->menu->retrieve($route, $level, $userRoles);

        return $menu;
    }

    public function getName() {
        return 'btn.menu';
    }

}

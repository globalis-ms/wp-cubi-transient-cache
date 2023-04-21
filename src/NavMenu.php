<?php

namespace Globalis\WP\Cubi\TransientCache;

class NavMenu
{
    public static function hooks()
    {
        if (defined('WP_CUBI_TRANSIENT_CACHE_DISABLE_AUTO_CACHE_NAV_MENUS') && WP_CUBI_TRANSIENT_CACHE_DISABLE_AUTO_CACHE_NAV_MENUS) {
            return;
        }
        add_filter('pre_wp_nav_menu', [__CLASS__, 'get'], 10, 2);
    }

    public static function get($null, $args)
    {
        remove_filter('pre_wp_nav_menu', [__CLASS__, 'get'], 10, 2);

        $key = 'nav-menu-' . md5(serialize($args));

        $html = Cache::get($key, 'menus');

        if (!$html) {
            $args = (array) $args;
            $args['echo'] = false;
            $html = wp_nav_menu($args);

            Cache::set($key, $html, 'menus');
        }

        add_filter('pre_wp_nav_menu', [__CLASS__, 'get'], 10, 2);

        return $html;
    }
}

<?php

namespace Globalis\WP\Cubi\TransientCache;

class NavMenu
{
    public static function hooks()
    {
        add_filter('pre_wp_nav_menu', [__CLASS__, 'get'], 10, 2);
    }

    public static function get($null, $args)
    {
        remove_filter('pre_wp_nav_menu', [__CLASS__, 'get'], 10, 2);

        $cacheKey = 'nav-menu-' . md5(serialize($args));

        $html = Cache::get($cacheKey, 'menus');

        if (!$html) {
            $args = (array) $args;
            $args['echo'] = false;
            $html = wp_nav_menu($args);

            Cache::set($cacheKey, 'menus', $html);
        }

        add_filter('pre_wp_nav_menu', [__CLASS__, 'get'], 10, 2);

        return $html;
    }
}

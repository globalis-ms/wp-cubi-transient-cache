<?php

namespace Globalis\WP\Cubi\TransientCache;

class Cache
{
    protected static $cleared = [];
    protected static $scheduledPingHomeUrl = false;

    public static function hooks()
    {
        add_action('init', [__CLASS__, 'registerClearHooks'], 10);
        add_action('init', [__CLASS__, 'detectSiteUrlChanged'], 20);
    }

    public static function get(string $cacheKey, string $group)
    {
        $transient = 'wp_cubi_cache_' . $group . '_' . $cacheKey;

        if (function_exists('pll_current_language') && !empty(pll_current_language())) {
            $transient .= '_' . pll_current_language();
        }

        $cached = get_transient($transient);

        return empty($cached) ? null : $cached;
    }

    public static function set(string $cacheKey, string $group, mixed $value)
    {
        $transient = 'wp_cubi_cache_' . $group . '_' . $cacheKey;

        if (function_exists('pll_current_language') && !empty(pll_current_language())) {
            $transient .= '_' . pll_current_language();
        }

        set_transient($transient, $value);
    }

    public static function getGroups()
    {
        $groups = [
            'all',
            'menus',
            'posts',
        ];

        return apply_filters('wp-cubi\transient-cache\groups', $groups);
    }

    public static function getClearHooks()
    {
        $hooks = [
            // ALL :
            'wp-cubi\transient-cache\clear' => ['all'],
            'wp-cubi\transient-cache\site-url-changed' => ['all'],
            // MENUS :
            'update_option_theme_mods_' . get_option('stylesheet') => ['menus'],
            'wp_update_nav_menu' => ['menus'],
            'wp_ajax_menu-locations-save' => ['menus'],
            'wp_ajax_customize_save' => ['menus'],
            // POSTS :
            'acf/save_post' => ['posts'],
            'save_post' => ['posts'],
            'acf/save_post' => ['posts'],
            'edited_terms' => ['posts'],
            'created_term' => ['posts'],
            'delete_term' => ['posts'],
        ];

        return apply_filters('wp-cubi\transient-cache\clear-hooks', $hooks);
    }

    public static function registerClearHooks()
    {
        foreach (self::getClearHooks() as $hook => $groups) {
            add_action($hook, function () use ($groups) {
                self::clear($groups);
            }, 99);
        }
    }

    public static function clear(array $groups = ['all'])
    {
        if (!is_array($groups)) {
            $groups = [$groups];
        }

        foreach ($groups as $group) {
            if (in_array($group, self::$cleared)) {
                return;
            }

            if ($group === 'all') {
                $prefix = '_transient_wp_cubi_cache_';
            } else {
                $prefix = '_transient_wp_cubi_cache_' . $group;
            }

            $options = wp_load_alloptions();

            foreach (array_keys($options) as $option) {
                if (0 === strpos($option, $prefix)) {
                    delete_option($option);
                }
            }

            self::$cleared[] = $group;
        }

        self::schedulePingHomeUrl();
    }

    public static function detectSiteUrlChanged()
    {
        $siteUrlHash  = base64_encode(home_url('/'));
        $siteUrlHashCached  = get_option('wp-cubi-site-url-hash');

        if (empty($siteUrlHashCached) || $siteUrlHashCached !== $siteUrlHash) {
            update_option('wp-cubi-site-url-hash', $siteUrlHash, true);
            do_action('wp-cubi\transient-cache\site-url-changed');
        }
    }

    public static function schedulePingHomeUrl()
    {
        if (self::$scheduledPingHomeUrl) {
            return;
        }

        add_action('shutdown', [__CLASS__, 'pingHomeUrl']);

        self::$scheduledPingHomeUrl = true;
    }

    public static function pingHomeUrl()
    {
        $url  = add_query_arg('action', 'wp-cubi-transient-cache-ping', home_url('/'));
        $args = ['timeout' => 0.01, 'blocking'  => false, 'sslverify' => false];
        wp_remote_post($url, $args);
    }
}

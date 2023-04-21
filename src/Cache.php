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

    public static function set(string $key, mixed $value, string $group = 'all')
    {
        if (defined('WP_CUBI_TRANSIENT_CACHE_BYPASS_ALL') && WP_CUBI_TRANSIENT_CACHE_BYPASS_ALL) {
            return;
        }

        $transient = 'wp_cubi_cache_' . $group . '_' . $key;

        if (function_exists('pll_current_language') && !empty(pll_current_language())) {
            $transient .= '_' . pll_current_language();
        }

        set_transient($transient, $value);
    }

    public static function get(string $key, string $group = 'all')
    {
        if (defined('WP_CUBI_TRANSIENT_CACHE_BYPASS_ALL') && WP_CUBI_TRANSIENT_CACHE_BYPASS_ALL) {
            return null;
        }

        $transient = 'wp_cubi_cache_' . $group . '_' . $key;

        if (function_exists('pll_current_language') && !empty(pll_current_language())) {
            $transient .= '_' . pll_current_language();
        }

        $cached = get_transient($transient);

        return empty($cached) ? null : $cached;
    }

    public static function clear(string $key, string $group = 'all')
    {
        $transient = 'wp_cubi_cache_' . $group . '_' . $key;

        if (function_exists('pll_current_language') && !empty(pll_current_language())) {
            $transient .= '_' . pll_current_language();
        }

        delete_transient($transient);
    }

    public static function clearGroups(array $groups = ['all'])
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
                self::clearGroups($groups);
            }, 99);
        }
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

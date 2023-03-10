<?php

namespace Globalis\WP\Cubi\TransientCache;

use function Globalis\WP\Cubi\add_action;

add_action('muplugins_loaded', function () {
    if (defined('WP_CUBI_DISABLE_TRANSIENT_CACHE') && WP_CUBI_DISABLE_TRANSIENT_CACHE) {
        return;
    }

    Cache::hooks();

    if (!defined('WP_CUBI_DISABLE_NAV_MENUS_AUTO_CACHE') || !WP_CUBI_DISABLE_NAV_MENUS_AUTO_CACHE) {
        NavMenu::hooks();
    }
});

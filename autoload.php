<?php

namespace Globalis\WP\Cubi\TransientCache;

use function Globalis\WP\Cubi\add_action;

add_action('muplugins_loaded', function () {
    Cache::hooks();
    NavMenu::hooks();
});

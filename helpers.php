<?php

use Globalis\WP\Cubi\TransientCache\Cache;
use Globalis\WP\Cubi\TransientCache\NavMenu;
use Globalis\WP\Cubi\TransientCache\Template;

function wp_cubi_cache_get($key, $group)
{
    return Cache::get($key, $group);
}

function wp_cubi_cache_set($key, $group, $value)
{
    return Cache::set($key, $group, $value);
}

function wp_cubi_get_template_part_cached($file, array $data = [], $group = 'all', bool $return = false)
{
    return Template::get($file, $data, $group, $return);
}

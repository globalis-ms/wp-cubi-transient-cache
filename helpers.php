<?php

use Globalis\WP\Cubi\TransientCache\Cache;
use Globalis\WP\Cubi\TransientCache\NavMenu;
use Globalis\WP\Cubi\TransientCache\Template;

function wp_cubi_cache_set(string $key, mixed $value, string $group = 'all')
{
    return Cache::set($key, $value, $group);
}

function wp_cubi_cache_get(string $key, string $group = 'all')
{
    return Cache::get($key, $group);
}

function wp_cubi_cache_clear(string $key, string $group = 'all')
{
    return Cache::clear($key, $group);
}

function wp_cubi_cache_clear_group(string $group = 'all')
{
    return Cache::clearGroups([$group]);
}

function wp_cubi_cache_clear_groups(array $groups = ['all'])
{
    return Cache::clearGroups($groups);
}

function wp_cubi_get_template_part_cached(string $file, array $data = [], string $group = 'all', bool $return = false)
{
    return Template::get($file, $data, $group, $return);
}

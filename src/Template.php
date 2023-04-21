<?php

namespace Globalis\WP\Cubi\TransientCache;

class Template
{
    public static function get(string $file, array $data = [], string $group = 'all', bool $return = false)
    {
        if (defined('WP_CUBI_TRANSIENT_CACHE_BYPASS_TEMPLATES') && WP_CUBI_TRANSIENT_CACHE_BYPASS_TEMPLATES) {
            $value = \Globalis\WP\Cubi\include_template_part($file, $data, true);
        } else {
            $key = $file;

            $value = Cache::get($key, $group);

            if (!$value) {
                $value = \Globalis\WP\Cubi\include_template_part($file, $data, true);
                Cache::set($key, $value, $group);
            }
        }

        if ($return) {
            return $value;
        } else {
            echo $value;
        }
    }
}

<?php

namespace Globalis\WP\Cubi\TransientCache;

class Template
{
    public static function get(string $file, array $data = [], string $group = 'all', bool $return = false)
    {
        if (defined('WP_CUBI_DISABLE_CACHE_TEMPLATES') && WP_CUBI_DISABLE_CACHE_TEMPLATES) {
            $html = \Globalis\WP\Cubi\include_template_part($file, $data, true);
        } else {
            $cacheKey = $file;

            $html = Cache::get($cacheKey, $group);

            if (!$html) {
                $html = \Globalis\WP\Cubi\include_template_part($file, $data, true);
                Cache::set($cacheKey, $group, $html);
            }
        }

        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }
}

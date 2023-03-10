<?php

namespace Globalis\WP\Cubi\TransientCache;

class Template
{
    public static function get($file, array $data = [], $group = 'all', bool $return = false)
    {
        $cacheKey = $file;

        $html = Cache::get($cacheKey, $group);

        if (!$html) {
            $html = \Globalis\WP\Cubi\include_template_part($file, $data, true);
            Cache::set($cacheKey, $group, $html);
        }

        if ($return) {
            return $html;
        } else {
            echo $html;
        }
    }
}

# [wp-cubi-transient-cache](https://github.com/globalis-ms/wp-cubi-transient-cache)

[![PHP Version Require](https://img.shields.io/packagist/dependency-v/globalis/wp-cubi-transient-cache/php?color=%233fb911)](https://github.com/globalis-ms/wp-cubi-transient-cache/blob/master/composer.json)
[![Latest Stable Version](https://poser.pugx.org/globalis/wp-cubi-transient-cache/v/stable)](https://packagist.org/packages/globalis/wp-cubi-transient-cache)
[![License](https://poser.pugx.org/globalis/wp-cubi-transient-cache/license)](https://github.com/globalis-ms/wp-cubi-transient-cache/blob/master/LICENSE.md)

Persistent cache library based on WordPress transients

[![wp-cubi](https://github.com/globalis-ms/wp-cubi/raw/master/.resources/wp-cubi-500x175.jpg)](https://github.com/globalis-ms/wp-cubi/)

## Features

- Provides `Cache::set()` and `Cache::get()` methods, using transients
- Provides `Template::get()` method, that automatically caches required template part
- Automatically cache WordPress nav-menus out of the box
- Clear cache by group, when menus are saved, when posts are saved, when site URL changes

## Installation

```
composer require globalis/wp-cubi-transient-cache
```

## Usage

### Generic caching

Save a value in cache :

```php
<?php

use Globalis\WP\Cubi\TransientCache\Cache;

$my_value = my_expensive_function();
Cache::set('my_key', $my_value, 'my_group');
```

Get a value from cache :

```php
<?php

use Globalis\WP\Cubi\TransientCache\Cache;

$my_value_cached = Cache::get('my_key', 'my_group');
```

Clear a single cache entry :

```php
<?php

use Globalis\WP\Cubi\TransientCache\Cache;

Cache::clear('my_key', 'my_group');
```

Clear multiple cache entries :

```php
<?php

use Globalis\WP\Cubi\TransientCache\Cache;

Cache::clearGroups(['my_group']);
```

### Templates caching

Get a template part from cache :

```php
<?php

use Globalis\WP\Cubi\TransientCache\Template;

$html = Template::get('templates/my-part', 'my_group');
```

When template part cache doesn't exists, `Template::get()` will automatically loads required template part, and cache it.

Clearing template part uses generic clear method, using file path as key (e.g. `Cache::clear('templates/my-part', 'my_group');`).

### Nav-menus caching

Nav-menus are automatically cached, but multiple default hooks are setup out of the box to clear this cache when needed.

You can always manually clear all menus cache with :

```php
<?php

use Globalis\WP\Cubi\TransientCache\Cache;

Cache::clearGroups(['menus']);
```

### Procedural style

Most of the above methods can be called in a procedural style, without any namespace :

```php
<?php

wp_cubi_cache_set(string $key, mixed $value, string $group = 'all');
wp_cubi_cache_get(string $key, string $group = 'all');
wp_cubi_cache_clear(string $key, string $group = 'all');
wp_cubi_cache_clear_group(string $group = 'all');
wp_cubi_cache_clear_groups(array $groups = ['all']);
wp_cubi_get_template_part_cached(string $file, array $data = [], string $group = 'all', bool $return = false);
```

## Configuration

Bypass cache when developping, or in a specific environment config file :

```php
<?php

define('WP_CUBI_TRANSIENT_CACHE_BYPASS_ALL', true);
```

Bypass cache only for template parts :

```php
<?php

define('WP_CUBI_TRANSIENT_CACHE_BYPASS_TEMPLATES', true);
```

Disable nav-menus automatic caching :

```php
<?php

define('WP_CUBI_TRANSIENT_CACHE_DISABLE_AUTO_CACHE_NAV_MENUS', true);
```

## Hooks

Clear all cache :

```
do_action('wp-cubi\transient-cache\clear');
```

Filter clear hooks :

```
apply_filters('wp-cubi\transient-cache\clear-hooks', $hooks);
```

## Development

Before opening pull requests, please check and apply project coding standards with `./vendor/bin/phpcs .` and/or `./vendor/bin/phpcbf .`

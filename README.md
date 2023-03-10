# TODO

- Add a wp-cli command : `wp cubi-transient-cache clear [group]`
- Add a webhook : `<site-url>/?wp-cubi-transient-cache-clear=<group>`
- sanitize transient keys, or already handled by core functions ?
- implement caching PSR ?
- cron task to clear all cache periodically with a constant `bool|int MAX_LIFE`
- add into wp-cubi main repository
- add remote `wp cubi-transient-cache clear` call on wp-cubi default deploy command

WPU Base File Cache
---

A class to handle basic file cache.


## Insert in construct

```php
/* Cache */
require_once __DIR__ . '/inc/WPUBaseFileCache/WPUBaseFileCache.php';
$this->wpubasefilecache = new \mypluginid\WPUBaseFileCache('mypluginid');

```

## Insert when needed

```php
$cache_key = 'mycachekey';
$query_value = $this->wpubasefilecache->get_cache($cache_key, 24 * 60 * 60);
if (!$query_value) {
    $query_value = 'BIG QUERY';
    $this->wpubasefilecache->set_cache($cache_key, $query_value);
}
```

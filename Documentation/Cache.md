# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Cache

These page will show you on how-to use ```\Lollipop\Cache``` 

### save(\$key, \$data, \$force = false, \$ttl = 1440) ```(void)```
Save cache for data.

**Parameters:**

- ```key``` Cache key
- ```data``` Data to be cached
- ```force``` If theres is an existing cache override it
- ```ttl``` Time-to-leave (default to 24 Hrs)

```php

use \Lollipop\Cache;

Cache::save('message', 'Hello World!');


```

### recover(\$key) ```(string)```
Recover saved cache.

```php

use \Lollipop\Cache;

Cache::save('message', 'Hello World!');

echo Cache::recover('message');

```

### exists(\$key) ```(string)```
Check if cache is existing.

### remove(\$key) ```(string)```
Remove cache from storage.

### purge() ```(string)```
Purge all stored cache.

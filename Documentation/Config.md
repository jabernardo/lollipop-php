# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Config

These page will show you on how-to use ```\Lollipop\Config``` 

###```add($key, $value)``` (void)
Add configuration

```php

use \Lollipop\Config;

Config::add('pages', ['page1', 'page2']);

```

###```get($key = '')``` (mixed)
Get configuration value

###```remove($key)``` (void)
Remove configuration key
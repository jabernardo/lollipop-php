# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Config

These page will show you on how-to use ```\Lollipop\Config``` 

### add($key, $value) ```(void)```
Add or set configuration

> Use `.` (dot) as separator to declare levels for configuration

```php

use \Lollipop\Config;

Config::add('pages.home.title', 'Home Page');
Config::set('pages.about.title', 'About Us');

```

### get($key = '') ```(mixed)```
Get configuration value

```php

use \Lollipop\Config;

Config::get('pages')->home->title;

```

### remove($key)``` (void)```
Remove configuration key

```php

Config::remove('pages.about');

```

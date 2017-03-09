# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Cookie

These page will show you on how-to use ```\Lollipop\Cookie``` 

### set(\$key, \$value, \$path = '/', \$expiration = 2592000) ```(void)```
Create or update a cookie

```php

use \Lollipop\Cookie;

Cookie::set('session_id', rand());

```

### get(\$key) ```(mixed)```
Get value of cookie

```php

use \Lollipop\Cookie;

echo Cookie::get('session_id');

```

### exists(\$key) ```(bool)```
Check if cookie exists

### drop(\$key, \$path = '/') ```(void)```
Remove cookie

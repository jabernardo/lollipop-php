# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Request

These page will show you on how-to use ```\Lollipop\Request``` 

### is($requests) ```(bool)```
Check if expected request are fulfilled

```php

use \Lollipop\Request;


if (Request::is(['username', 'password'])) {
    // Then login
}


```

### get($requests = null) ```(array)```
Get request received

```php

use \Lollipop\Request;

$user_info = Request::get(['username', 'fullname', 'age']);

```


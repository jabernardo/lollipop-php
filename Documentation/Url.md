# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Url

These page will show you on how-to use ```\Lollipop\Url``` 

### base($url = '', $cacheBuster = false) ```(string)```
Get base url.

```php
<?php

use \Lollipop\Url;

echo Url::base(); // http://www.domain.com
echo Url::base('static/css/style.css'); // http://www.domain.com/static/css/style.css
echo Url::base('static/css/style.css', true); // http://www.domain.com/static/css/style.css?1.0

```

### here() ```(string)```
Alias request URI

### alive($url) ```(bool)```
Is URL alive?

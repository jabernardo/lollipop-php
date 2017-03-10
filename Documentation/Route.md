# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Route

These page will show you on how-to use ```\Lollipop\Route``` 

## Routing
Lollipop's Routing system is very simple

### get($path, $callback, $cachable = false, $cache_time = 24) ```(void)```
### post($path, $callback, $cachable = false, $cache_time = 24) ```(void)```
### put($path, $callback, $cachable = false, $cache_time = 24) ```(void)```
### delete($path, $callback, $cachable = false, $cache_time = 24) ```(void)```
### all($path, $callback, $cachable = false, $cache_time = 24) ```(void)```
> You can defined your own method by using ```serve``` function
### serve($method, $path, $callback, $cachable = false, $cache_time = 24) ```(void)```

## Setting the headers

```php

use \Lollipop\Route;

Route::all('/', function() {
    Route::setHeader('Content-Disposition: attachment; filename="name.json"');
    
    return ['name' => 'John Aldrich Bernardo'];
});


```

## Forwarding response
Let's you'll be removing to new routing keyword and don't want to lose your
visitors you can forward the action to another route. It'll run like a 301 header.

```php

use \Lollipop\Route;

Route::all('/newpage', function() {
    return 'New Page'; 
});

Route::all('/oldpage', function() {
    Route::forward('newpage'); 
});


```

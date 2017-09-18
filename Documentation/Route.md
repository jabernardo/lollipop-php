# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Route

These page will show you on how-to use ```\Lollipop\Route``` 

## Configure [.htaccess](http://www.htaccess-guide.com/)
Please use below code as the default for your ```.htaccess```
Or see [LMVC](http://github.com/jabernardo/lmvc) for routing using 
[Lollipop](https://github.com/jabernardo/lollipop-php)

```apache
# Enable running of scripts
AddHandler cgi-script .pl .cgi
Options +ExecCGI +FollowSymLinks

# Disable Indexing of Directories
Options -Indexes

# Enable RewriteEngine
RewriteEngine on

# Enable page access 
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d

# Redirect all pages to index
RewriteRule ^(.*)$/? index.php [L]
```

## Writing your index file
As you've seen in default ```.htaccess``` file, all request we'll be summitted
to ```index.php``` so we'll be setting it up to handle requests.

```php
<?php

require_once('/path/to/lollipop/autoload.php');

use \Lollipop\Route;

Route::all('/', function() {
    return 'Hello World!';
});

```

## Routing
Lollipop's Routing system is very simple

### serve($method, $path, $callback, $cachable = false, $cache_time = 24) ```(void)```

Serve a method for a specific request.
Also you could use our pre-defined methods.

```php
<?php

use \Lollipop\Route;

/**
 * Serve url with any request method
 *
 * @param   string  $path           URL path
 * @param   callback/string         Callback or Controller.Action
 * @param   bool    $cachable       Is URL cacheable (default is `false`)
 * @param   int     $cache_time     Cache time in minutes
 *
 * all($path, $callback, $cachable = false, $cache_time = 1440) {...
 *
 */
Route::all('/', function() {
    return 'Hello World!';
}, true);

```
> If you want to use specific request method for routing you can change `all` with
`get`, `post`, `put`, `delete` or define a new one using `Route::serve`

```php
<?php

// get, post, put or delete
Route::get('/get', function() {
    return 'GET'; 
});

// user defined
Route::serve('sample', '/sample', function() {
   return 'sample'; 
});

// Multiple request
Route::serve(['GET', 'POST'], '/getorpost', function() {
    return 'GET or POST';
});

```

## Parameters
Getting beautified URL parameters

- ```(%s)``` String
- ```(%d)``` Numbers
- ```(%%)``` Any character

```php
<?php

use \Lollipop\Route;

Route::get('/profile/(%s)', function($username) {
   // @todo Insert code here 
});


```


## Setting the headers

```php
<?php

use \Lollipop\Route;

Route::all('/', function() {
    Route::setHeader('Content-Disposition: attachment; filename="name.json"');
    
    return ['name' => 'John Aldrich Bernardo'];
});


```

## Prepare & Clean
Prepare and Clean callbacks will enable you to set custom function to run before
or after a route executed.

```php
<?php

use \Lollipop\Route;

Route::prepare(function($param) { // Route::clean(function($param) {
    // Functions here
}, $param);

```

## Forwarding response
Let's you'll be removing to new routing keyword and don't want to lose your
visitors you can forward the action to another route. It'll run like a 301 header.

```php
<?php

use \Lollipop\Route;

Route::all('/newpage', function() {
    return 'New Page'; 
});

Route::all('/oldpage', function() {
    return Route::forward('newpage'); 
});


```

## Request Headers

`lollipop-gzip` (true/false)
- Force gzip compression

## Response Headers

`lollipop-forwarded` (true/false)
- Will be set if `Route::forward` was called

`lollipop-cache` (true/false)
- Is page from cache?

`lollipop-cache-saved` (true/false)
- If new cache is saved

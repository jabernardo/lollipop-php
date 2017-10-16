# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\HTTP

These page will show you on how-to use ```\Lollipop\HTTP``` 

## Route

### Configure [.htaccess](http://www.htaccess-guide.com/)
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

### Writing your index file
As you've seen in default ```.htaccess``` file, all request we'll be summitted
to ```index.php``` so we'll be setting it up to handle requests.

```php
<?php

require_once('/path/to/lollipop/autoload.php');

use \Lollipop\HTTP\Route;

Route::all('/', function($req, $res) {
    return $res->set('Hello World!');
});

```

### Routing
Lollipop's Routing system is very simple

```php
<?php
[
   'path'       => '/',
   'method'     => ['GET', 'POST'],
   'callback'   => 'Controller.index', // or function
   'cacheable'  => true, // or false
   'cache_time' => 1440, // in minutes
   'before'     => ['MiddleWare1', 'MiddleWare2'],
   'after'      => ['MiddleWare3', function(Response $res, $args)]
]
```

#### serve($route) ```:void```

Serve a method for a specific request.
Also you could use our pre-defined methods.

```php
<?php

use \Lollipop\HTTP\Route;

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

Route::serve([
    'path' => 'sample',
    'method' => 'GET',
    'callback' => function($req, $res) {
        return $res->set('This is a sample');
    }
]);

// Multiple request
Route::serve([
    'path' => 'sample',
    'method' => ['GET', 'POST'],
    'callback' => function($req, $res) {
        return $res->set('This is a sample');
    }
]);

```

### Parameters
Getting beautified URL parameters

- ```(%s)``` String
- ```(%d)``` Numbers
- ```(%%)``` Any character

```php
<?php

use \Lollipop\HTTP\Route;

Route::get('/profile/(%s)', function($username) {
   // @todo Insert code here 
});


```


### Setting the headers

```php
<?php

use \Lollipop\HTTP\Route;

Route::all('/', function($req, $res) {
    $res->header('Content-Disposition: attachment; filename="name.json"');
    
    return $res->set(['name' => 'John Aldrich Bernardo']);
});


```

### Prepare & Clean
Prepare and Clean callbacks will enable you to set a global middleware to run before
or after a route executed.

```php
<?php

use \Lollipop\HTTP\Route;

Route::prepare(function($req, $res, ...$args) { // Route::clean(function($req, $res) {
    // Functions here
});

```

### Forwarding response
Let's you'll be removing to new routing keyword and don't want to lose your
visitors you can forward the action to another route. It'll run like a 301 header.

```php
<?php

use \Lollipop\HTTP\Route;

Route::all('/newpage', function($req, $res, ...$args) {
    return 'New Page'; 
});

Route::all('/oldpage', function($req, $res, ...$args) {
    return Route::forward('newpage', $req, $res, $args);
});


```

### Request Headers

`lollipop-gzip` (true/false)
- Force gzip compression

### Response Headers

`lollipop-forwarded` (true/false)
- Will be set if `Route::forward` was called

`lollipop-cache` (true/false)
- Is page from cache?

## Response

**Creating a new Response**

`Lollipop\HTTP\Route` uses `\Lollipop\HTTP\Response` for dispatching results from callback.
For example.

```php
<?php

\Lollipop\HTTP\Route::get('/', function() {
    return 'Hello World!';
});

```

Router will deploy an Response object containing output from callback.
Also you could also return a new Response object from a Route

```php
<?php

\Lollipop\Route::get('/', function() {
    return (new Response([1, 2, 3]))
                ->compress();
});

```

### get() ```:mixed```

Get Response content.

### compress($enabled = true) ```:object```

Enable gzip compression for response.

> Returns Response instance

### set($data) ```:object```

Like in construct. This will set the content for Response.

> Returns Response instance

### header($headers) ```:object```

Add header for response.

Parameters:

- ```$headers``` - ```string|array``` Headers to be added

> Returns Response instance

### getHeaders() ```:array```

Get Response headers set in ```Response::header```

### render() ```:void```

Apply HTTP headers and print-out response content.


## Request

### is($requests) ```:bool```
Check if expected request are fulfilled

```php
<?php

$req = \Lollipop\HTTP\Request();

if ($req->is(['username', 'password'])) {
    // Then login
}

```

### get($requests = null) ```:array```
Get request received

```php
<?php

$req = \Lollipop\HTTP\Request();

$user_info = $req->get(['username', 'fullname', 'age']);

```

### send(array $options) ```:mixed```
Simple cURL wrapper with Caching feature

#### Configurations:
```php
<?php

$config = [
    // Request configurations
    "request": [
            // Auto convert JSON to Object (enabled by default)
            "json" => true, // or false
            "cache" => [
                "enable" => true, // or false
                "time" => 1440 // 24 hours (in minutes)
            ]
        ]
]

```
> Make sure to set `localdb` for cookie storage
> and `cache` for caching features

Creating a simple request

```php
<?php

$req = \Lollipop\HTTP\Request();

$response = $req->send([
        'url' => 'https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty'
    ]);

```

Profiling request

```php
<?php

$req = \Lollipop\HTTP\Request();

$response = $req->send([
        'profile' => true,
        'url' => 'https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty'
    ]);

```
These will return

```json

{
    "url": "https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty",
    "headers": [ ],
    "time": 0.6267831326,
    "status": 200,
    "payload": "...",
    "cache": true
}

```

More options...

```php
<?php

$req = \Lollipop\HTTP\Request();

$response = $req->send([
        'profile' => true, // Profiling results (disabled by default)
        'url' => 'https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty',
        'cache' => true, // Cache toggle override
        'auth' => [ // Auth
            'user' => 'username',
            'pwd' => 'your_password'
        ],
        'headers' => [
            'lollipop-gzip: true' // you request headers
        ],
        'timeout' => 0, // Request timeout
        'follow' => true, // Follow URL redirections
        'max-redirections' => 5, // Max redirections for following URL
        'return-headers' => false, // Include headers in return
        'no-body' => false, // Return no-body
        'user-agent' => '...', // Wanted user-agent
        'referrer' => '...', // Set HTTP_REFERRER
        'method' => 'PUT', // Custom request method
        'parameters' => [ // Post parameters
                'key' => 'value'
            ],
        'cookie-jar' => 'file_location', //  Cookie jar
        'cookie-file' => 'file_location', // Cookie file
    ]);

```

# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Request

These page will show you on how-to use ```\Lollipop\Request``` 

### is($requests) ```(bool)```
Check if expected request are fulfilled

```php
<?php

use \Lollipop\Request;

if (Request::is(['username', 'password'])) {
    // Then login
}


```

### get($requests = null) ```(array)```
Get request received

```php
<?php

use \Lollipop\Request;

$user_info = Request::get(['username', 'fullname', 'age']);

```

### send(array $options) ```mixed```
Simple cURL wrapper with Caching feature

#### Configurations:
```json

[
    // Request configurations
    "request": [
            // Auto convert JSON to Object (enabled by default)
            "json": true, // or false
            "cache": [
                "enable": true, // or false
                "time": 1440 // 24 hours (in minutes)
            ]
        ]
]

```
> Make sure to set `localdb` for cookie storage
> and `cache` for caching features

Creating a simple request

```php
<?php

use \Lollipop\Request;

$response = Request::send([
        'url' => 'https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty'
    ]);

```

Profiling request

```php
<?php

use \Lollipop\Request;

$response = Request::send([
        'profile' => true,
        'url' => 'https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty'
    ]);

```
These will return

```json

{
    url: "https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty",
    headers: [ ],
    time: 0.6267831326,
    status: 200,
    payload: ...,
    cache: true
}

```

More options...

```php
<?php

use \Lollipop\Request;

$response = Request::send([
        'profile' => true, // Profiling results (disabled by default)
        'url' => 'https://hacker-news.firebaseio.com/v0/item/8863.json?print=pretty',
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

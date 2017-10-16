# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Config

These page will show you on how-to use ```\Lollipop\Config``` 

> To configure your ```Lollipop``` application you just need to send an array to 
> ```\Lollipop\Config::load``` function

```php
<?php

$config = [
    'app' => [
        'name' => 'Application Name',
        'version' => '1.0'
    ],
    'db' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'database_name',
        'cache' => true,
        'cache_time' => 1440
    ],
    'environment' => 'dev',
    'dev_tools' => true
];

\Lollipop\Config::load($config);


```

### Configurations for your application

#### app ```:array```
- ```version``` (Application version)

#### autoload ```:array```
Folder(s) to include in autoload

#### output ```:array```
- ```compression``` Enable output compression (gzip)


#### sugar ```:string```
Override encryption key

#### db ```:array```
- ```host``` Hostname for database
- ```username``` UID for connection
- ```password``` Password for authentication
- ```database``` Database name
- ```cache``` Enable or disable results caching
- ```cache_time``` Cache expiration in minutes

#### text ```:array```
* ```security```
+ ```key``` The key.
+ ```method``` See [openssl methods](https://secure.php.net/manual/en/function.openssl-get-cipher-methods.php)
+ ```iv``` Initialization Vector

#### dev_tools ```:bool```
Activate developers tools for your own benefit. Options below only triggered by request (GET/POST) sent to application.

- ```purge_all_cache``` Purges all stored cache

#### environment ```:string```
Set current environment used

- ```dev``` or ```development```
- ```stg``` or ```staging```
- ```prd``` or ```production```

#### page_not_found ```:array```
Page not found properties

- ```show``` Show or hide page not founds
- ```route``` Route for custom 404 Page Not Found

#### cache ```:array```

- ```folder``` Path for cache
- ```driver``` Cache driver (sqlite/filesystem)

> When using `sqlite` please do define `localdb` in config

#### localdb ```:array```

- ```folder``` Path to localdb storage

#### log ```:array```

- ```enable``` True/False
- ```folder``` Path for logs
- ```hourly``` Enable hourly format in log files

#### anti_xss ```:bool```

Enable or Disable XSS injection in ```Page::render```

#### overrides ```:array```
These allows you to override configuration based from environment set.

```php

<?php

$config = [
    'app' => [
        'name' => 'Application Name',
        'version' => '1.0'
    ],
    'db' => [
        'host' => 'localhost',
        'username' => 'root',
        'password' => '',
        'database' => 'database_name',
        'cache' => true,
        'cache_time' => 1440
    ],
    'environment' => 'dev',
    'dev_tools' => true,
    'overrides' => [
        'stg' => [
            'dev_tools' => false
        ]
    ]
];

```

### add($key, $value) ```:void```
Add or set configuration

> Use `.` (dot) as separator to declare levels for configuration

```php
<?php

use \Lollipop\Config;

Config::add('pages.home.title', 'Home Page');
Config::set('pages.about.title', 'About Us');

```

### get($key = '') ```:mixed```
Get configuration value

```php
<?php

use \Lollipop\Config;

Config::get('pages')->home->title;

```

### remove($key) ```:void```
Remove configuration key

```php
<?php

Config::remove('pages.about');

```

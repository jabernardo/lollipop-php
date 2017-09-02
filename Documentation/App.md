# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\App

These page will show you on how-to use ```\Lollipop\App``` 

## Configure [Lollipop](https://github.com/jabernardo/lollipop-php)

To configure your ```Lollipop``` application you just need to send an array to 
```\Lollipop\App::init``` function

```php

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

\Lollipop\App::init($config);


```

### Configurations for your application

#### app ```(array)```
- ```version``` (Application version)

#### autoload ```(array)```
Folder(s) to include in autoload

#### output ```(array)```
- ```compression``` Enable output compression (gzip)

#### db ```(array)```
- ```host``` Hostname for database
- ```username``` UID for connection
- ```password``` Password for authentication
- ```database``` Database name
- ```cache``` Enable or disable results caching
- ```cache_time``` Cache expiration in minutes

#### text ```(array)```
* ```security```
+ ```key``` The key.
+ ```method``` See [openssl methods](https://secure.php.net/manual/en/function.openssl-get-cipher-methods.php)
+ ```iv``` Initialization Vector

#### dev_tools ```(bool)```
Activate developers tools for your own benefit. Options below only triggered by request (GET/POST) sent to application.

- ```purge_all_cache``` Purges all stored cache

#### environment ```(string)```
Set current environment used

- ```dev``` or ```development```
- ```stg``` or ```staging```
- ```prd``` or ```production```

#### show_not_found ```(bool)```
Show or hide 404 page

#### not_found_page ```(string)```
Change 404 page

#### cache ```(array)```

- ```folder``` Path for cache
- ```driver``` Cache driver (sqlite/filesystem)

> When using `sqlite` please do define `localdb` in config

#### localdb ```(array)```

- ```folder``` Path to localdb storage

#### log ```(array)```

- ```folder``` Path for logs


### getResponseTime ```(double)```
Returns applications response time

### getBenchmark ```(array)```
Returns applications memory used and response time

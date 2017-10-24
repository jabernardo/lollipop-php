# [Lollipop](http://github.com/jabernardo/lollipop-php) ![Travis: Build Status](https://travis-ci.org/jabernardo/lollipop-php.svg?branch=master "Travis: Build Status")

A slim and very flexible framework for PHP

###### IS LOLLIPOP FOR YOU?
You can use Lollipop when you're creating a simple and powerful web application. Lollipop removed the weight of large frameworks. Also Lollipop was created to offer cool functionalities and speed.

###### IT's EASY!

> Configure [.htaccess](http://www.htaccess-guide.com/).
Use below code as the default for your ```.htaccess```
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

# Redirect request to index
RewriteCond %{REQUEST_FILENAME} !-f
RewriteCond %{REQUEST_FILENAME} !-d
RewriteRule ^(.*)$/? index.php [L]
```

A simple `Hello World` Page

```php
<?php

require('/path/to/lollipop-php/autoload.php');
// or if using composer
// require('./vendor/autoload.php');

// Your index page
\Lollipop\HTTP\Route::get('/', function($request, $response) {
    return $response->set('Hello World!');
}, true);

```
###### DOWNLOAD
Get the latest version of Lollipop-PHP framework from our Git repository hosted on GitHub
```bash
git clone https://github.com/jabernardo/lollipop-php.git lollipop-php
```
or get it via ```composer```
```bash
composer require "jabernardo/lollipop-php"
```
and start the development of your projects.

###### RESOURCES

See libraries documentation [here](https://github.com/jabernardo/lollipop-php/tree/master/Documentation).

## License

The LMVC framework is open-sourced software licensed under the [MIT license](http://opensource.org/licenses/MIT).

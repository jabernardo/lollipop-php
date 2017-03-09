# Starting [Lollipop](http://github.com/4ldrich/lollipop-php)
This will guide you on using Lollipop-PHP framework.

## Downloading
First make sure you have a Lollipop

```bash
git clone https://github.com/4ldrich/lollipop-php.git lollipop-php
```

## Configure [.htaccess](http://www.htaccess-guide.com/)
Please use below code as the default for your ```.htaccess```
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

## Do more!
Now that you have a setup you could do more!

# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Session

These page will show you on how-to use ```\Lollipop\Session``` 

### start() ```(void)```
Start session

### stop() ```(void)```
Stop session

### exists($key) ```(void)```
Checks if a session variable exists

```php
<?php

use \Lollipop\Session;

// session_start() or Session::start() isn't required to call
if (Session::exists('userid')) {
    // to do something here...
}

```

### key() ```(string)```
Returns the key used in encrypting session variables

```php
<?php

use \Lollipop\Session;

// The generated key will be used by \Lollipop\Text library as salt for
// session value
echo Session::key();


```


### set($key, $value) ```(void)```
Creates a new session or sets an existing sesssion

```php
<?php

use \Lollipop\Session;

// Create session variable
// NOTE: Value is encrypted using \Lollipop\Text
Session::set('userid', $userid);

```


### get($key) ```(string)```
Gets session variable's value

```php
<?php

use \Lollipop\Session;

// Decrypts and gets the value of session variable
Session::get('userid');


```

### drop($key) ```(void)```
Removes a session variable


```php
<?php


use \Lollipop\Session;

Session::drop('userid');


```

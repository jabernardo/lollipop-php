# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Text

These page will show you on how-to use ```\Lollipop\Text``` 


### contains($haystack, $needle) ```:bool```
Checks if a string contains another string

```php
<?php

use \Lollipop\Text;

Text::contains('Hello World!', 'Hello'); // true


```

### lock($string, $key = null) ```:string```
Encrypt string

### unlock($cipher, $key = null) ```:string```
Decrypt string

### escape($string) ```:string```
Alias addslashes

### entities($string) ```:string```
Returns HTML displayable string

### random($length) ```:string```
Generate a random string

### split($string, $token) ```:string```
Splits string


## Filter

```php
<?php

use \Lollipop\Text\Filter;


```

### name($string) ```:bool```
Checks if string is valid as name

### contact($string) ```:bool```
Checks if string is valid as contact

### email($string) ```:bool```
Checks if string is valid as email

### url($string) ```:bool```
Checks if string is valid as url

### ip($string) ```:bool```
Checks if string is valid as ip


## Inflector

```php
<?php

use \Lollipop\Text\Inflector;

```

### camelize($str) ```:string```
Camelize string. (e.g.) ```john_aldrich``` to ```JohnAldrich```

### filename($str) ```:string```
Convert string into a valid filename string case

### humanize($str) ```:string```
Convert string into human readable format

### underscore($str) ```:string```
Convert spaces into underscores

### url($str) ```:string```
Convert into string a safe url string

# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\CsrfToken

These page will show you on how-to use ```\Lollipop\CsrfToken``` 

### get() ```:string```
Get a random token

```php
<?php

use \Lollipop\CsrfToken;

$token = CsrfToken::get();

```

### getName() ```:string```
Get a token name

```php
<?php

use \Lollipop\CsrfToken;

$token_name = CsrfToken::getName();

```

### getFormInput() ```:string```
Get hidden input for anti_csrf

```php
<form action="" method="post">
<?= \Lollipop\CsrfToken::getFormInput() ?>
```

### isValid($token) ```:bool```
Check if token is valid

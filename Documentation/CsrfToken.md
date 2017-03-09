# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\CsrfToken

These page will show you on how-to use ```\Lollipop\CsrfToken``` 

### get() ```(string)```
Get a random token

```php
use \Lollipop\CsrfToken;

$token = CsrfToken::get();

```

### isValid($token) ```(bool)```
Check if token is valid

### hook($die = true)```(mixed)```
Returns ```bool``` if ```$die``` is ```true``` then kill the entire application

```php
use \Lollipop\CsrfToken;

CsrfToken::hook();

```

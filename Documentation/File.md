# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\File

These page will show you on how-to use ```\Lollipop\File``` 


## Opening or creating a new file

```php

use \Lollipop\File;

$f = new File('sample.txt');


```

### contents($contents = null) ```(bool)```
Get or set file contents

```php

use \Lollipop\File;

$f = new File('sample.txt');
$f->contents('Hello World!');

echo $f->contents();

```

### size() ```float```
Return file size

# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\File

These page will show you on how-to use ```\Lollipop\File``` 


## Opening or creating a new file

```php
<?php

use \Lollipop\File;

$f = new File('sample.txt');


```

### contents($contents = null) ```:bool```
Get or set file contents

```php
<?php

use \Lollipop\File;

$f = new File('sample.txt');
$f->contents('Hello World!');

echo $f->contents();

```

### temp() ```:object```
Mark file as temporary. Will delete file after unset.

```php

use \Lollipop\File;

$f = (new File('sample.txt'))->temp();
$f->contents('Hello World!');

echo $f->contents();
unset($f); // Will delete file

```

### size() ```:float```
Return file size

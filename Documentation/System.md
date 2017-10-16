# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\System

These page will show you on how-to use ```\Lollipop\System``` 


## File

### write($filename, $contents, $overwriteExisting = true) ```:void```
Write contents to file

```php
<?php

use \Lollipop\System\File;

File::write('sample.txt', 'Hello World!');

```

### read($filename) ```:mixed```
Get file contents

### size($filename, $returnFormatted = false) ```:double```
Get file size() ```float```

### delete($filename) ```:void```
Delete or unlink file

### exists($filename) ```:bool```
Check if file exists

## Directory

### exists($directory) ```:bool```
Check if directory exists

```php
<?php

use \Lollipop\System\Directory;

Directory::exists('Docs');

```

### contents($directory) ```:array```
Get directory contents using iteration

### delete($directory, $force = false) ```:mixed```
Delete directory.
Enable ```force``` to delete files inside directory


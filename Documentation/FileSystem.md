# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\FileSystem

These page will show you on how-to use ```\Lollipop\FileSystem``` 

### fileWrite($filename, $contents, $overwriteExisting = true) ```:void```

```php
<?php

use \Lollipop\FileSystem;

FileSystem::fileWrite('sample.txt', 'Hello World!');

```

### fileRead($filename) ```:mixed```
Get file contents

### fileSize($filename, $returnFormatted = false) ```:double```
Get file size() ```float```

### fileDelete($filename) ```:void```
Delete or unlink file

### fileExists($filename) ```:bool```
Check if file exists

### directoryExists($directory) ```:bool```
Check if directory exists

### directoryContents($directory) ```:array```
Get directory contents using iteration

### directoryDelete($directory, $force = false) ```:mixed```

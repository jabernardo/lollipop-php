# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Benchmark

These page will show you on how-to use ```\Lollipop\Benchmark``` 

### mark($mark) ```(void)```
Mark this time.

```php
<?php

use \Lollipop\Benchmark;

$start = Benchmark::mark('start');

// wait for 2 seconds
usleep(2000000);

$end = Benchmark::mark('end');

```

### elapsed($start, $end) ```(mixed)```
Compute memory and time used between marks

```php
<?php

use \Lollipop\Benchmark;

$start = Benchmark::mark('start');

// wait for 2 seconds
usleep(2000000);

$end = Benchmark::mark('end');

echo Benchmark::elapsed($start, $end);

```

### elapsedMemory($start, $end, $real_usage = false, $inMB = true) ```(mixed)```
Get elapsed memory between two marks

```php
<?php

use \Lollipop\Benchmark;

$start = Benchmark::mark('start');

// wait for 2 seconds
usleep(2000000);

$end = Benchmark::mark('end');

echo Benchmark::elapsedMemory($start, $end);

```

### elapsedTime($start, $end) ```(mixed)```
Compute the elapsed time of two marks

```php
<?php

use \Lollipop\Benchmark;

$start = Benchmark::mark('start');

// wait for 2 seconds
usleep(2000000);

$end = Benchmark::mark('end');

echo Benchmark::elapsedTime($start, $end);

```

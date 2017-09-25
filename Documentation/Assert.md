# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Assert

These page will show you on how-to use ```\Lollipop\Assert```.
`Assert` library provides a simple set of functions for assertion tests.

### equals($obj1, $obj2) ```:bool```
Simple comparison using ```==``` operator.

```php
<?php

\Lollipop\Assert::equals('1', 1);   // true
\Lollipop\Assert::equals(1, 1);     // true

```

### strictEquals($obj1, $obj2) ```:bool```
Strict type comparison using ```===``` operator.

```php
<?php

\Lollipop\Assert::strictEquals('1', 1); // false
\Lollipop\Assert::strictEquals(1, 1);   // true

```

### notEquals($obj1, $obj2) ```:bool```
Simple comparison using ```!=``` operator.

```php
<?php

\Lollipop\Assert::notEquals('1', 1);   // false
\Lollipop\Assert::notEquals(1, 1);     // false

```

### strictNotEquals($obj1, $obj2) ```:bool```
Simple comparison using ```!==``` operator.

```php
<?php

\Lollipop\Assert::strictNotEquals('1', 1);   // true
\Lollipop\Assert::strictNotEquals(1, 1);     // false

```

### true($obj)
Check object if `true`.

```php
<?php

\Lollipop\Assert::true(false); // false

```

### false($obj)
Check object if `false`.

```php
<?php

\Lollipop\Assert::false(false); // true

```

### exception($callback) ```:bool```
Check if callback will throw an exception.

```php
<?php

function err() {
    throw new \Exception('Error here');
}

\Lollipop\Assert::exception('err'); // true

```

### noException($callback) ```:bool```
Check if callback will not throw an exception.

```php
<?php

function err() {
    throw new \Exception('Error here');
}

\Lollipop\Assert::noException('err'); // false

```

# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Database

These page will show you on how-to use ```\Lollipop\Database``` 


## Configurations
In order to use the ```Database``` library make sure you have set the configuration ```db```

```php

use \Lollipop\App;
use \Lollipop\Config;

$config = array(
        'db' => array(
            'host' => 'localhost',
            'username' => 'root',
            'password' => '',
            'database' => 'database_name'
        )
    );

App::init($config);

```

## Selecting 

```Database``` library is a query builder, calling ```execute``` function will run the built SQL query
These may return a array if SQL statement is a query and returns an ```array``` of results, else 
it will return the ```mysqli_result``` object.

> Cache is enabled by default

### Basic Select

```php

use \Lollipop\Database;


$sql = Database::table('users')
        ->select('*'); // or selectAll();

echo 'SQL Statement: ', $sql, '<br>';
echo 'SQL Results:<br>';

var_dump($sql->execute());


```

### Selective Select

```php

use \Lollipop\Database;


$sql = Database::table('users')
        ->select(['username', 'password']);
```

### Filtering results

```php

use \Lollipop\Database;


$sql = Database::table('users')
        ->where('username', '4ldrich')
        ->where('age', '=', 21)
        ->orWhere('email', '4ldrich')
        ->select(['username', 'password']);
```

### Joins

```php

use \Lollipop\Database;


$sql = Database::table('users')
        ->join('location', 'location.id', '=', 'users.location_id')
        // Can use leftJoin or rightJoin
        ->selectAll();

```

## Inserting Data 

```Database::insert``` requires ```array(key=>value)``` as parameter

```php

use \Lollipop\Database;

$sql = Database::table('products')
        ->insert([
                'sku' => 'ABCDE-56',
                'qty' => 10.0,
                'price_per_price' => 5.0
            ]);

```

## Updating Data 

```php

use \Lollipop\Database;

$sql = Database::table('products')
        ->where('sku', 'ABCDE-56')
        ->update([
                'qty' => 10.0,
                'price_per_price' => 5.0
            ]);

```

## Increment and Decrement

```php

use \Lollipop\Database;

$sql = Database::table('cart')
        ->where('id', 1)
        ->where('sku', 'ABCDE-56')
        ->increment('qty', 1);
        // ->decrement('qty', 1);
```

## Remove

```php

use \Lollipop\Database;


$sql = Database::table('users')
        ->where('username', '4ldrich')
        ->remove();
        
```

## Raw SQL

```php

use \Lollipop\Database;

$users = Database::raw('SELECT * FROM users')->execute();

```


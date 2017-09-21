# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Response

These page will show you on how-to use ```\Lollipop\Response``` 

## Creating a new Response

`Lollipop\Route` uses `\Lollipop\Response` for dispatching results from callback.
For example.

```php
<?php

\Lollipop\Route::get('/', function() {
    return 'Hello World!';
});

```

Router will deploy an Response object containing output from callback.
Also you could also return a new Response object from a Route

```php
<?php

\Lollipop\Route::get('/', function() {
    return (new Response([1, 2, 3]))
                ->compress();
});

```

## Functions

### get() ```:mixed```

Get Response content.

### compress($enabled = true) ```:object```

Enable gzip compression for response.

> Returns Response instance

### set($data) ```:object```

Like in construct. This will set the content for Response.

> Returns Response instance

### header($headers) ```:object```

Add header for response.

Parameters:

- ```$headers``` - ```string|array``` Headers to be added

> Returns Response instance

### getHeaders() ```:array```

Get Response headers set in ```Response::header```

### render() ```:void```

Apply HTTP headers and print-out response content.

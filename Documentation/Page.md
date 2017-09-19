# \\[Lollipop](https://github.com/jabernardo/lollipop-php)\Page

These page will show you on how-to use ```\Lollipop\Page``` 

### render($view, array $data = array()) ```:mixed```
Render a view file

```php

// view.php

Hello <?= isset($name) ? $name : 'World'; ?>!

```

```php

// Renderer

use \Lollipop\Page;

echo Page::render('view.php', ['name' => 'John']);

```

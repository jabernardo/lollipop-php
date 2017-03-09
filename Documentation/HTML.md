# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\HTML

These page will show you on how-to use ```\Lollipop\HTML``` 


### doc() ```(void)```
Write document header for HTML5

### charset($charset = 'UTF-8') ```void```
Write ```<meta http-equiv="Content-Type" content="text/html; charset='UTF-8'/>```

### css($p2css) ```void```
Link a css to document

### favicon($ico, $type = 'image/png', $rel = 'icon') ```void```
Link a favicon

```php

use \Lollipop\HTML;

HTML::favicon('/favicon.png');

```

### meta(array $attr) ```void```
Embed meta tag

```php

use \Lollipop\HTML;

HTML::meta(['name' => 'keyword', 'value' => 'Values goes here']);

```

### link(array $attr) ```void```
Embed link tag

```php

use \Lollipop\HTML;

HTML::link(['rel' => 'stylesheet', 'href' => '//style.css']);

```

### image($src, array $attr = array()) ```void```
Link an image

### anchor($href, $alias, array $attr = array()) ```void```
Create a link

### script($src, $type = 'text/javascript') ```void```
Embed an external javascript

### nestedList(array $data, $tag = 'ul', array $attr = array()) ```void```
Convert array to HTML list element

### table(array $data, array $attr = array(), $firstRowHeader = false, $lastRowFooter = false) ```void```
Convert array into table

### p($str, array $attr = array()) ```void```
Create a paragraph

### label($str, array $attr = array()) ```void```
Create a label

### elem($name, array $attr = array(), $closing = false) ```void```
Create an element

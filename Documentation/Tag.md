# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\Tag

These page will show you on how-to use ```\Lollipop\Tag``` 


### Creating a tag

```php

use \Lollipop\Tag;

$tag = Tag::create('p')
        ->add('class', 'error')
        ->add('class', 'log')
        ->add('id', 'message')
        ->contains('Hello');

```

### Create an empty tag

```php

use \Lollipop\Tag;

$empty_tag = Tag::create('meta', true)
                ->add('charset', 'utf-8');

```


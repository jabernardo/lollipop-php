# \\[Lollipop](http://github.com/4ldrich/lollipop-php)\User

These page will show you on how-to use ```\Lollipop\User``` 

## Tracking active user

### in($uid = '', $role = 'USER', $anchor = null) ```(int)```
Log in user or check if user is logged

> -1 Not connected to database, 1 if users are too many, and 0 if logged in.

```php

use \Lollipop\User;

User::in('4ldirch', 'USER'); // Track user as active
User::in(); // Returns <true> if there is an active user


```

### getUsername() ```(mixed)```
Get username of active user

### isAdmin() ```(bool)```
Is active user have an admin role

### hasRole($role) ```(bool)```
Check if active has the specified role

### out() ```(void)```
Log out active user

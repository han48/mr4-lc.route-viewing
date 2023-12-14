# Lock screen

Only one use can be view page in one time.

## Screenshot



## Installation

```bash
composer require mr4-lc/route-viewing
php artisan vendor:publish --tag=mr4-lc-route-viewing --force
```

## Configuration

config/mr4lc-route-viewing.php
```php
[
    'lock' => [
        'suffix' => '/sedit',
    ],
    'port' => 8090,
    'config' => [
        '0' => [
            'user_class' => App\Models\User::class,
            'display_name' => 'name',
        ],
        '1' => [
            'user_class' => Encore\Admin\Auth\Database\Administrator::class,
            'display_name' => 'name',
        ],
    ],
]
```

## Usage

vite.config.js
- Add 'resources/js/mr4lc-route-viewing.js'
```
input: [
    'resources/css/app.css', 'resources/js/app.js',
    'resources/js/mr4lc-route-viewing.js'
],
```

resources/js/app.js
- Add import './mr4lc-route-viewing'
```
import './mr4lc-route-viewing'
```

Add to layout template
```
<x-mr4-lc.route-viewing-header />
@vite(['resources/css/app.css', 'resources/js/app.js'])
```

If you use laravel-admin.
```
<x-mr4-lc.route-viewing-header config="1" />
@vite(['resources/js/mr4lc-route-viewing.js'])
```

Start websocket
```
php artisan websocket:init
```

## License

Licensed under The [MIT License (MIT)](https://github.com/han48/mr4-lc.select-birthday/blob/main/LICENSE).

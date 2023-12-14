<?php

return [
    'lock' => [
        'suffix' => '/sedit',
    ],
    'port' => 8090,
    'ws_url' => 'ws://127.0.0.1:8090',
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
];

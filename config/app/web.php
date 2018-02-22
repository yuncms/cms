<?php

return [
    'components' => [
        'request' => [
            'class' => yuncms\web\Request::class,
        ],
        'response' => [
            'class' => yuncms\web\Response::class,
        ],
        'user' => [
            'class' => yuncms\web\User::class,
            'enableAutoLogin' => true,
            'loginUrl' => ['/user/security/login'],
            'identityClass' => 'yuncms\models\User',
        ],
    ]
];
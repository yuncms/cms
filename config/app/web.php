<?php

return [
    'bootstrap' => [
        'log', 'queue',
    ],
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
            'on afterLogin' => function () {
                Yii::$app->queue->push(new UserLastVisitJob(['user_id' => Yii::$app->user->getId(), 'time' => time()]));
            }
        ],
    ]
];
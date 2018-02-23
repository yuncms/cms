<?php

return [
    'components' => [
        'db' => [
            'class' => yii\db\Connection::class,
            'charset' => 'utf8',
            'tablePrefix' => 'yun_',
        ],
        'cache' => [
            'class' => yii\caching\FileCache::class,
        ],
        'queue' => [
            'class' => yii\queue\sync\Queue::class,
        ],
        'authClientCollection' => [
            'class' => yii\authclient\Collection::class,
        ],
        'settings' => [
            'class' => yuncms\components\Settings::class,
            'frontCache' => 'cache'
        ],
        'notifications' => [
            'class' => yuncms\notifications\NotificationManager::class
        ],
    ]
];
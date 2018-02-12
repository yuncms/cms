<?php

return [
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'charset' => 'utf8',
            'tablePrefix' => 'yun_',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'queue' => [
            'class' => 'yii\queue\sync\Queue',
        ],
        'authClientCollection' => [
            'class' => 'yii\authclient\Collection',
        ],
        'settings' => [
            'class' => 'yuncms\components\Settings',
            'frontCache' => 'cache'
        ],
    ]
];
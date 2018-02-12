<?php

return [
    'class' => \yuncms\web\Application::class,
    'components' => [
        'request' => [
            'class' => yuncms\web\Request::class,
        ],
        'response' => [
            'class' => yuncms\web\Response::class,
        ],
        'urlManager' => [
            'class' => yuncms\web\UrlManager::class,
            'enablePrettyUrl' => true,
            'ruleConfig' => ['class' => yuncms\web\UrlRule::class],
        ],
    ]
];
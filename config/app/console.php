<?php

return [
    'class' => \yuncms\console\Application::class,
    'bootstrap' => [
        'queue',
    ],
    'components' => [
        'request' => yuncms\console\Request::class,
        'user' => yuncms\console\User::class,
    ],
    'controllerMap' => [
        'migrate' => yuncms\console\controllers\MigrateController::class,
        'templateFile' => '@yuncms/views/migration.php',
        'migrationNamespaces' => array_values($migrationNamespaces),
    ],
];
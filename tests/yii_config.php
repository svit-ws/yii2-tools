<?php

return [
    'id' => 'test-app',
    'basePath' => __DIR__,
    'vendorPath' => dirname(__DIR__) . '/vendor',
    'components' => [
        'db' => [
            'class' => 'yii\db\Connection',
            'dsn' => 'sqlite:tests/_output/common.db',
            'username' => 'root',
            'password' => '',
            'charset' => 'utf8',
        ],
    ]
];

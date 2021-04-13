<?php

use yii\db\Connection;

return [
    'class' => Connection::class,
    'dsn' => 'mysql:host=127.0.0.1;dbname=company',
    'username' => 'root',
    'password' => 'secret',
    'charset' => 'utf8',

    // Schema cache options (for production environment)
    'enableLogging' => false,
    'enableProfiling' => false,
    'enableSchemaCache' => true,
    'enableQueryCache' => true,
    'schemaCacheDuration' => 60,
    'schemaCache' => 'cache',
];

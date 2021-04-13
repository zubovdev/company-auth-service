<?php

use yii\log\FileTarget;
use yii\rbac\DbManager;
use yii\rest\UrlRule;
use yii\swiftmailer\Mailer;
use app\models\User;
use yii\caching\FileCache;
use yii\web\ErrorHandler;
use yii\web\JsonParser;
use yii\web\JsonResponseFormatter;
use yii\web\MultipartFormDataParser;
use yii\web\Request;
use yii\web\Response;

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

return [
    'id' => 'company.auth',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'components' => [
        'authManager' => [
            'class' => DbManager::class,
        ],
        'request' => [
            'class' => Request::class,
            'cookieValidationKey' => 'FP6MkcCGTq-a8N23zMeZifCh5kaBUnn2',
            'enableCsrfValidation' => false,
            'baseUrl' => '/',
            'parsers' => [
                'application/json' => JsonParser::class,
                'multipart/form-data' => MultipartFormDataParser::class,
            ],
        ],
        'response' => [
            'class' => Response::class,
            'acceptMimeType' => 'application/json',
            'charset' => 'UTF-8',
            'format' => 'json',
            'formatters' => [
                'json' => [
                    'class' => JsonResponseFormatter::class,
                    'prettyPrint' => YII_DEBUG | YII_ENV_DEV,
                    'encodeOptions' => JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE,
                ],
            ],
        ],
        'cache' => [
            'class' => FileCache::class,
        ],
        'user' => [
            'identityClass' => User::class,
            'enableAutoLogin' => false,
            'enableSession' => false,
        ],
        'errorHandler' => [
            'class' => ErrorHandler::class,
            'errorAction' => null,
        ],
        'mailer' => [
            'class' => Mailer::class,
            'useFileTransport' => false,
            'transport' => [
                'class' => 'Swift_SmtpTransport',
                'host' => $params['smtp.host'],
                'username' => $params['smtp.user'],
                'password' => $params['smtp.pass'],
                'port' => $params['smtp.port'],
                'encryption' => 'ssl',
            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => FileTarget::class,
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
                [
                    'class' => UrlRule::class,
                    'controller' => 'chat-message',
                    'patterns' => [
                        'GET' => 'index',
                        'POST' => 'create',
                        '' => 'options',
                    ],
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'stream-message',
                ],
                [
                    'class' => UrlRule::class,
                    'controller' => 'user',
                    'extraPatterns' => [
                        'GET user' => 'user',
                        'POST <action:(login)>' => '<action>',
                        'PUT {id}/<action:(change-password)>' => 'change-password',
                        'OPTIONS <action:(login|change-password|user)>' => 'options',
                    ],
                ],
            ],
        ],
    ],
    'params' => $params,
];

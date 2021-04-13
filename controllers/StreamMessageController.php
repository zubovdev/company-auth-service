<?php

namespace app\controllers;

use app\models\StreamMessage;
use app\search\StreamMessageSearch;
use yii\data\ActiveDataFilter;
use yii\filters\AccessControl;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

/**
 * Class StreamMessageController
 * @package app\controllers
 */
class StreamMessageController extends ActiveController
{
    public $modelClass = StreamMessage::class;
    public $updateScenario = StreamMessage::SCENARIO_UPDATE;

    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        $behaviors = parent::behaviors();
        $behaviors['authenticator'] = [
            'class' => HttpBearerAuth::class,
            'except' => ['options'],
        ];
        $behaviors['access'] = [
            'class' => AccessControl::class,
            'except' => ['options'],
            'rules' => [
                [
                    'allow' => true,
                    'roles' => ['@'],
                    'actions' => ['index', 'create'],
                ],
                [
                    'allow' => true,
                    'roles' => ['admin'],
                ],
            ],
        ];
        return $behaviors;
    }

    /**
     * {@inheritDoc}
     */
    public function actions(): array
    {
        $actions = parent::actions();
        $actions['index']['dataFilter'] = [
            'class' => ActiveDataFilter::class,
            'searchModel' => StreamMessageSearch::class,
        ];
        return $actions;
    }
}

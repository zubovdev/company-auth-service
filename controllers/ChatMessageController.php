<?php

namespace app\controllers;

use app\models\ChatMessage;
use app\search\MessageSearch;
use yii\data\ActiveDataFilter;
use yii\filters\auth\HttpBearerAuth;
use yii\rest\ActiveController;

/**
 * Class ChatMessageController
 * @package app\controllers
 */
class ChatMessageController extends ActiveController
{

    public $modelClass = ChatMessage::class;

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
            'searchModel' => MessageSearch::class,
        ];
        unset($actions['view'], $actions['update'], $actions['delete']);
        return $actions;
    }

}

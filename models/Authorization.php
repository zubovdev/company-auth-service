<?php

namespace app\models;

use Yii;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class Authorization
 *
 * @property int $id
 * @property int $userId
 * @property string $token
 * @property User $user
 *
 * @package app\models
 */
class Authorization extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        $this->token = Yii::$app->security->generateRandomString();

        return true;
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }
}

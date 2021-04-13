<?php

namespace app\common\models;

use app\models\User;
use yii\behaviors\BlameableBehavior;
use yii\behaviors\TimestampBehavior;
use yii\db\ActiveQuery;
use yii\db\ActiveRecord;

/**
 * Class Message
 *
 * @property int $id
 * @property int $userId
 * @property string $text
 * @property int $createTime
 * @property User $user
 *
 * @package app\common\models
 */
abstract class Message extends ActiveRecord
{
    /**
     * {@inheritDoc}
     */
    public function behaviors(): array
    {
        return [
            'timestamp' => [
                'class' => TimestampBehavior::class,
                'createdAtAttribute' => 'createTime',
                'updatedAtAttribute' => false,
            ],
            'blameable' => [
                'class' => BlameableBehavior::class,
                'createdByAttribute' => 'userId',
                'updatedByAttribute' => false,
            ],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            ['text', 'required'],
            ['text', 'string', 'max' => 1024],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function extraFields(): array
    {
        return ['user'];
    }

    /**
     * @return ActiveQuery
     */
    public function getUser(): ActiveQuery
    {
        return $this->hasOne(User::class, ['id' => 'userId']);
    }
}

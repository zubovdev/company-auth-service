<?php

namespace app\models;

use app\common\models\Message;

/**
 * Class StreamMessage
 *
 * @property int $status
 *
 * @package app\models
 */
class StreamMessage extends Message
{
    public const SCENARIO_UPDATE = 'update';

    /**
     * {@inheritDoc}
     */
    public function scenarios(): array
    {
        return [
            self::SCENARIO_DEFAULT => ['text'],
            self::SCENARIO_UPDATE => ['status'],
        ];
    }

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return array_merge(parent::rules(), [
            ['status', 'integer'],
            ['status', 'in', 'range' => [-1, 0, 1]],
        ]);
    }

    /**
     * {@inheritDoc}
     */
    public function beforeSave($insert): bool
    {
        if (!parent::beforeSave($insert)) {
            return false;
        }

        if ($insert) {
            $this->status = 0;
        }

        return true;
    }
}

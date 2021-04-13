<?php

namespace app\search;

use yii\base\Model;

/**
 * Class MessageSearch
 * @package app\search
 */
class MessageSearch extends Model
{
    /**
     * @var int|null
     */
    public ?int $id = null;
    /**
     * @var int|null
     */
    public ?int $createTime = null;

    /**
     * {@inheritDoc}
     */
    public function rules(): array
    {
        return [
            [['createTime', 'id'], 'integer'],
        ];
    }
}

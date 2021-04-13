<?php

namespace app\search;

/**
 * Class StreamMessageSearch
 * @package app\search
 */
class StreamMessageSearch extends MessageSearch
{
    /**
     * @var int|null
     */
    public ?int $status = null;

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
}

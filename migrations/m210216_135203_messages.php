<?php

use yii\db\Migration;

/**
 * Class m210216_135203_messages
 */
class m210216_135203_messages extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('chat_message', [
            'id' => $this->primaryKey(),
            'text' => $this->text(),
            'userId' => $this->integer(),
            'createTime' => $this->integer(),
        ]);
        $this->addForeignKey('fk-chat_message-userId-user-id', 'chat_message', 'userId', 'user', 'id', 'SET NULL', 'CASCADE');

        $this->createTable('stream_message', [
            'id' => $this->primaryKey(),
            'text' => $this->text(),
            'userId' => $this->integer(),
            'status' => $this->tinyInteger(),
            'createTime' => $this->integer(),
        ]);

        $this->addForeignKey('fk-stream_message-userId-user-id', 'stream_message', 'userId', 'user', 'id', 'SET NULL',
            'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-stream_message-userId-user-id', 'stream_message');
        $this->dropTable('stream_message');

        $this->dropForeignKey('fk-chat_message-userId-user-id', 'chat_message');
        $this->dropTable('chat_message');
    }
}

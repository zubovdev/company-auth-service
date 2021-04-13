<?php

use yii\db\Migration;

/**
 * Class m210215_155946_auth_table
 */
class m210215_155946_auth_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('authorization', [
            'id' => $this->primaryKey(),
            'userId' => $this->integer(),
            'token' => $this->string(32),
        ]);
        $this->addForeignKey('fk-authorization-userId-user-id', 'authorization', 'userId', 'user', 'id', 'CASCADE',
            'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('fk-authorization-userId-user-id', 'authorization');
        $this->dropTable('authorization');
    }
}

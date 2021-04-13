<?php

use yii\db\Migration;

/**
 * Class m210215_152032_create_schema
 */
class m210215_152032_create_schema extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('user', [
            'id' => $this->primaryKey(),
            'email' => $this->string(),
            'firstName' => $this->string(),
            'lastName' => $this->string(),
            'city' => $this->string(),
            'passwordHash' => $this->string(),
            'isActive' => $this->boolean(),
            'createTime' => $this->integer(),
            'updateTime' => $this->integer(),
        ]);

        $this->createTable('whitelist_email', [
            'id' => $this->primaryKey(),
            'email' => $this->string(),
        ]);

        $this->batchInsert('whitelist_email', ['email'], Yii::$app->params['whitelistEmails']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropTable('whitelist_email');
        $this->dropTable('user');
    }
}

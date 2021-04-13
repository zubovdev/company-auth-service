<?php

use yii\db\Migration;
use yii\db\Query;

/**
 * Class m210215_163253_admin
 */
class m210215_163253_admin extends Migration
{
    /**
     * {@inheritdoc}
     * @throws Exception
     */
    public function safeUp()
    {
        $this->insert('user', [
            'email' => 'admin@admin.ru',
            'firstName' => 'Admin',
            'lastName' => 'Admin',
            'passwordHash' => '$2y$13$9nPs3XLiDKLvceqj2LNXgO2Xdise1pLTdJc4N2E3Y/8ruHPgw2EbC',
            'createTime' => time(),
            'isActive' => true,
        ]);

        $adminId = (new Query)->from('user')
            ->select('id')
            ->where(['email' => 'admin@admin.ru'])
            ->one()['id'];

        $auth = Yii::$app->getAuthManager();
        $role = $auth->createRole('admin');
        $auth->add($role);
        $auth->assign($role, $adminId);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $auth = Yii::$app->getAuthManager();
        $auth->removeAll();

        $this->delete('user', ['email' => 'admin@admin.ru']);
    }
}

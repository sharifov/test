<?php

use yii\db\Migration;

/**
 * Class m200305_151200_create_index_user_id_tbl_notofication
 */
class m200305_151200_create_index_user_id_tbl_notofication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-notifications-n_user_id', '{{%notifications}}', ['n_user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-notifications-n_user_id', '{{%notifications}}');
    }


}

<?php

use yii\db\Migration;

/**
 * Class m200305_150028_create_index_delete_tbl_notofication
 */
class m200305_150028_create_index_delete_tbl_notofication extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-notifications-n_deleted', '{{%notifications}}', ['n_deleted']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-notifications-n_deleted', '{{%notifications}}');
    }
}

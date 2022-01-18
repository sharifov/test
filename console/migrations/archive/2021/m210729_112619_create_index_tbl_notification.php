<?php

use yii\db\Migration;

/**
 * Class m210729_112619_create_index_tbl_notification
 */
class m210729_112619_create_index_tbl_notification extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-notifications-user-deleted', '{{%notifications}}', ['n_user_id', 'n_deleted']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-notifications-user-deleted', '{{%notifications}}');
    }
}

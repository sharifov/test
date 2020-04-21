<?php

use yii\db\Migration;

/**
 * Class m190515_144839_create_indexes
 */
class m190515_144839_create_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-email_e_inbox_email_id', '{{%email}}', ['e_inbox_email_id']);
        $this->createIndex('IND-call_c_call_status', '{{%call}}', ['c_call_status']);
        $this->createIndex('IND-call_c_created_user_id', '{{%call}}', ['c_created_user_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-email_e_inbox_email_id', '{{%email}}');
        $this->dropIndex('IND-call_c_call_status', '{{%call}}');
        $this->dropIndex('IND-call_c_created_user_id', '{{%call}}');
    }


}

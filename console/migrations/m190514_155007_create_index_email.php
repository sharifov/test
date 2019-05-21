<?php

use yii\db\Migration;

/**
 * Class m190514_155007_create_index_email
 */
class m190514_155007_create_index_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-email_e_communication_id', '{{%email}}', ['e_communication_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-email_e_communication_id', '{{%email}}');
    }
}

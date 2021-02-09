<?php

use yii\db\Migration;

/**
 * Class m200516_184500_remove_unique_key_from_conference_participant
 */
class m200516_184500_remove_unique_key_from_conference_participant extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropIndex('cp_call_sid', '{{%conference_participant}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->createIndex('cp_call_sid', '{{%conference_participant}}', ['cp_call_sid'], true);
    }
}

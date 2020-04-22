<?php

use yii\db\Migration;

/**
 * Class m190315_152419_email_create_indexes
 */
class m190315_152419_email_create_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('em_from_idx', '{{%email}}', 'e_email_from');
        $this->createIndex('em_to_idx', '{{%email}}', 'e_email_to');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('em_from_idx', '{{%email}}');
        $this->dropIndex('em_to_idx', '{{%email}}');
    }
}

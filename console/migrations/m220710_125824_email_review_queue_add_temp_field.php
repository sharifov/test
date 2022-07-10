<?php

use yii\db\Migration;

/**
 * Class m220710_125824_email_review_queue_add_temp_field
 */
class m220710_125824_email_review_queue_add_temp_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%email_review_queue}}', 'erq_email_is_norm', $this->boolean());
        $this->dropForeignKey('FK-email_review_queue-erq_email_id', '{{%email_review_queue}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%email_review_queue}}', 'erq_email_is_norm');
        $this->addForeignKey('FK-email_review_queue-erq_email_id', '{{%email_review_queue}}', 'erq_email_id', '{{%email}}', 'e_id', 'CASCADE', 'CASCADE');
    }
}

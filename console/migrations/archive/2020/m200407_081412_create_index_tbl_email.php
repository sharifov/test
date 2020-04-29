<?php

use yii\db\Migration;

/**
 * Class m200407_081412_create_index_tbl_email
 */
class m200407_081412_create_index_tbl_email extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-email-e_lead_id', '{{%email}}', 'e_lead_id');
        $this->createIndex('IND-email-e_type_id', '{{%email}}', 'e_type_id');
        $this->createIndex('IND-email-e_project_id', '{{%email}}', 'e_project_id');
        $this->createIndex('IND-email-e_created_dt', '{{%email}}', 'e_created_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-email-e_type_id', '{{%e_type_id}}');
        $this->dropIndex('IND-email-e_project_id', '{{%e_project_id}}');
        $this->dropIndex('IND-email-e_created_dt', '{{%email}}');
        $this->dropIndex('IND-email-e_lead_id', '{{%email}}');
    }
}

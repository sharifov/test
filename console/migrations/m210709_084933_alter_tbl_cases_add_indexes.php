<?php

use yii\db\Migration;

/**
 * Class m210709_084933_alter_tbl_cases_add_indexes
 */
class m210709_084933_alter_tbl_cases_add_indexes extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND_cases_category_id', '{{%cases}}', 'cs_category_id');
        $this->createIndex('IND_cases_status', '{{%cases}}', 'cs_status');
        $this->createIndex('IND_cases_user_id', '{{%cases}}', 'cs_user_id');
        $this->createIndex('IND_cases_dep_id', '{{%cases}}', 'cs_dep_id');
        $this->createIndex('IND_cases_project_id', '{{%cases}}', 'cs_project_id');
        $this->createIndex('IND_cases_client_id', '{{%cases}}', 'cs_client_id');
        $this->createIndex('IND_cases_last_action_dt', '{{%cases}}', 'cs_last_action_dt');
        $this->createIndex('IND_cases_order_uid', '{{%cases}}', 'cs_order_uid');
        $this->createIndex('IND_cases_cs_created_dt', '{{%cases}}', 'cs_created_dt');
        $this->createIndex('IND_cases_cs_updated_dt', '{{%cases}}', 'cs_updated_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND_cases_category_id', '{{%cases}}');
        $this->dropIndex('IND_cases_status', '{{%cases}}');
        $this->dropIndex('IND_cases_user_id', '{{%cases}}');
        $this->dropIndex('IND_cases_dep_id', '{{%cases}}');
        $this->dropIndex('IND_cases_project_id', '{{%cases}}');
        $this->dropIndex('IND_cases_client_id', '{{%cases}}');
        $this->dropIndex('IND_cases_last_action_dt', '{{%cases}}');
        $this->dropIndex('IND_cases_order_uid', '{{%cases}}');
        $this->dropIndex('IND_cases_cs_created_dt', '{{%cases}}');
        $this->dropIndex('IND_cases_cs_updated_dt', '{{%cases}}');
    }
}

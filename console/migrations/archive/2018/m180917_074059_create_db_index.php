<?php

use yii\db\Migration;

/**
 * Class m180917_074059_create_db_index
 */
class m180917_074059_create_db_index extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('tbl_leads_status_project_id_ind', '{{%leads}}', ['status', 'project_id']);
        $this->createIndex('tbl_quotes_status_ind', '{{%quotes}}', ['status']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('tbl_leads_status_project_id_ind', '{{%leads}}');
        $this->dropIndex('tbl_quotes_status_ind', '{{%quotes}}');
    }


}

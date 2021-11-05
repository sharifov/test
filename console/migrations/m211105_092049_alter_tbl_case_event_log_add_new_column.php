<?php

use yii\db\Migration;

/**
 * Class m211105_092049_alter_tbl_case_event_log_add_new_column
 */
class m211105_092049_alter_tbl_case_event_log_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%case_event_log}}', 'cel_category_id', $this->tinyInteger(1));
        $this->createIndex('IND-case_event_log-cel_category_id', '{{%case_event_log}}', 'cel_category_id');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%case_event_log}}', 'cel_category_id');
    }
}

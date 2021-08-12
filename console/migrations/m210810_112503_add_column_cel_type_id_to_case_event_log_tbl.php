<?php

use yii\db\Migration;

/**
 * Class m210810_112503_add_column_cel_type_id_to_case_event_log_tbl
 */
class m210810_112503_add_column_cel_type_id_to_case_event_log_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%case_event_log}}', 'cel_type_id', $this->tinyInteger()->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%case_event_log}}', 'cel_type_id');
    }
}

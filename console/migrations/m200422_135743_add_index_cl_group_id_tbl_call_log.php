<?php

use yii\db\Migration;

/**
 * Class m200422_135743_add_index_cl_group_id_tbl_call_log
 */
class m200422_135743_add_index_cl_group_id_tbl_call_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createIndex('IND-call_log-cl_group_id', '{{%call_log}}', ['cl_group_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-call_log-cl_group_id', '{{%call_log}}');
    }
}

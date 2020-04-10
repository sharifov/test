<?php

use yii\db\Migration;

/**
 * Class m200410_113338_add_columns_tbl_call
 */
class m200410_113338_add_columns_tbl_call extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_is_transfer', $this->boolean()->defaultValue(false));
        $this->addColumn('{{%call}}', 'c_queue_start_dt', $this->dateTime());
        $this->addColumn('{{%call}}', 'c_group_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'c_is_transfer');
        $this->dropColumn('{{%call}}', 'c_queue_start_dt');
        $this->dropColumn('{{%call}}', 'c_group_id');
    }
}

<?php

use yii\db\Migration;

/**
 * Class m210707_095215_add_column_to_tbl_user_statuses
 */
class m210707_095215_add_column_to_tbl_user_statuses extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%user_status}}', 'us_phone_ready_time', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%user_status}}', 'us_phone_ready_time');
    }
}

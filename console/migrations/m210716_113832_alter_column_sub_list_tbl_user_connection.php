<?php

use yii\db\Migration;

/**
 * Class m210716_113832_alter_column_sub_list_tbl_user_connection
 */
class m210716_113832_alter_column_sub_list_tbl_user_connection extends Migration
{
    /**
    * {@inheritdoc}
    */
    public function safeUp()
    {
        $this->alterColumn('{{%user_connection}}', 'uc_sub_list', $this->string(1400));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    }
}

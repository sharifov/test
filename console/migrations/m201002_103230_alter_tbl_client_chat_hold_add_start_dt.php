<?php

use yii\db\Migration;

/**
 * Class m201002_103230_alter_tbl_client_chat_hold_add_start_dt
 */
class m201002_103230_alter_tbl_client_chat_hold_add_start_dt extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%client_chat_hold}}', 'cchd_start_dt', $this->dateTime());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%client_chat_hold}}', 'cchd_start_dt');
    }
}

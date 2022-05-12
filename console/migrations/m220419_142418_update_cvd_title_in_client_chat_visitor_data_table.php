<?php

use yii\db\Migration;

/**
 * Class m220419_142418_update_cvd_title_in_client_chat_visitor_data_table
 */
class m220419_142418_update_cvd_title_in_client_chat_visitor_data_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_title', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_title', $this->string(150));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m220419_142418_update_cvd_title_in_client_chat_visitor_data_table cannot be reverted.\n";

        return false;
    }
    */
}

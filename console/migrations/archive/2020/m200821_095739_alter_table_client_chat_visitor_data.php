<?php

use yii\db\Migration;

/**
 * Class m200821_095739_alter_table_client_chat_visitor_data
 */
class m200821_095739_alter_table_client_chat_visitor_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_title', $this->string(150));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%client_chat_visitor_data}}', 'cvd_title', $this->string(50));
    }

    /*
    // Use up()/down() to run migration code without a transaction.
    public function up()
    {

    }

    public function down()
    {
        echo "m200821_095739_alter_table_client_chat_visitor_data cannot be reverted.\n";

        return false;
    }
    */
}

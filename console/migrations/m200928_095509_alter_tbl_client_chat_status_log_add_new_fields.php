<?php

use yii\db\Migration;

/**
 * Class m200928_095509_alter_tbl_client_chat_status_log_add_new_fields
 */
class m200928_095509_alter_tbl_client_chat_status_log_add_new_fields extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%client_chat_status_log}}', 'csl_user_id', $this->integer());
		$this->addColumn('{{%client_chat_status_log}}', 'csl_prev_channel_id', $this->integer());

		$this->addForeignKey('FK-client_chat_status_log-csl_user_id', '{{%client_chat_status_log}}', 'csl_user_id', '{{%employees}}', 'id', 'SET NULL', 'CASCADE');
		$this->addForeignKey('FK-client_chat_status_log-csl_prev_channel_id', '{{%client_chat_status_log}}', 'csl_prev_channel_id', '{{%client_chat_channel}}', 'ccc_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropForeignKey('FK-client_chat_status_log-csl_user_id', '{{%client_chat_status_log}}');
    	$this->dropForeignKey('FK-client_chat_status_log-csl_prev_channel_id', '{{%client_chat_status_log}}');

    	$this->dropColumn('{{%client_chat_status_log}}', 'csl_user_id');
    	$this->dropColumn('{{%client_chat_status_log}}', 'csl_prev_channel_id');
    }
}

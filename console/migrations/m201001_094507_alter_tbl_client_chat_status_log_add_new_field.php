<?php

use yii\db\Migration;

/**
 * Class m201001_094507_alter_tbl_client_chat_status_log_add_new_field
 */
class m201001_094507_alter_tbl_client_chat_status_log_add_new_field extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->addColumn('{{%client_chat_status_log}}', 'csl_action_type', $this->tinyInteger(2));
		$this->createIndex('IND-csl_action_type', '{{%client_chat_status_log}}', 'csl_action_type');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
    	$this->dropIndex('IND-csl_action_type', '{{%client_chat_status_log}}');
    	$this->dropColumn('{{%client_chat_status_log}}', 'csl_action_type');
    }
}

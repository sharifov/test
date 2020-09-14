<?php

use yii\db\Migration;

/**
 * Class m200812_105017_remove_composite_primary_key_from_client_user_access
 */
class m200812_105017_remove_composite_primary_key_from_client_user_access extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$this->dropForeignKey('FK-ccua_cch_id', '{{%client_chat_user_access}}');
		$this->dropForeignKey('FK-ccua_user_id', '{{%client_chat_user_access}}');
		$this->dropIndex('IND-ccua_user_id', '{{%client_chat_user_access}}');
		$this->dropPrimaryKey('PK-client_chat_user_access', '{{%client_chat_user_access}}');
    	$this->addColumn('{{%client_chat_user_access}}', 'ccua_id', $this->primaryKey()->first());
		$this->addForeignKey('FK-ccua_cch_id', '{{%client_chat_user_access}}', ['ccua_cch_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
		$this->addForeignKey('FK-ccua_user_id', '{{%client_chat_user_access}}', ['ccua_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
		$this->createIndex('IND-ccua_user_id', '{{%client_chat_user_access}}', ['ccua_user_id']);

	}

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropColumn('{{%client_chat_user_access}}', 'ccua_id');
//		$this->dropPrimaryKey('PK-client_chat_user_access', '{{%client_chat_user_access}}');
		$this->addPrimaryKey('PK-client_chat_user_access', '{{%client_chat_user_access}}', ['ccua_cch_id', 'ccua_user_id']);
//		$this->addForeignKey('FK-ccua_cch_id', '{{%client_chat_user_access}}', ['ccua_cch_id'], '{{%client_chat}}', ['cch_id'], 'CASCADE', 'CASCADE');
//		$this->addForeignKey('FK-ccua_user_id', '{{%client_chat_user_access}}', ['ccua_user_id'], '{{%employees}}', ['id'], 'CASCADE', 'CASCADE');
//		$this->createIndex('IND-ccua_user_id', '{{%client_chat_user_access}}', ['ccua_user_id']);
    }

}

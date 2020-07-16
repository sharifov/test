<?php

use yii\db\Migration;

/**
 * Class m200716_135633_create_tbl_client_chat_visitor
 */
class m200716_135633_create_tbl_client_chat_visitor extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
		$tableOptions = null;
		if ($this->db->driverName === 'mysql') {
			$tableOptions = 'CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci ENGINE=InnoDB';
		}

		$this->createTable('{{%client_chat_visitor}}', [
			'ccv_id' => $this->primaryKey(),
			'ccv_client_id' => $this->integer(),
			'ccv_visitor_rc_id' => $this->string(50)->unique()
		], $tableOptions);

		$this->addForeignKey('FK-clients-ccv_client_id', '{{%client_chat_visitor}}', ['ccv_client_id'], '{{%clients}}', ['id'], 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
		$this->dropForeignKey('FK-clients-ccv_client_id', '{{%client_chat_visitor}}');
		$this->dropTable('{{%client_chat_visitor}}');
    }
}

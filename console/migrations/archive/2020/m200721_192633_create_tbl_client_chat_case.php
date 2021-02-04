<?php

use yii\db\Migration;

/**
 * Class m200721_192633_create_tbl_client_chat_case
 */
class m200721_192633_create_tbl_client_chat_case extends Migration
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

        $this->createTable('{{%client_chat_case}}', [
            'cccs_chat_id' => $this->integer()->notNull(),
            'cccs_case_id' => $this->integer()->notNull(),
            'cccs_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_case-cccs-chat_id-case_id', '{{%client_chat_case}}', ['cccs_chat_id', 'cccs_case_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('PK-client_chat_case-cccs-chat_id-case_id', '{{%client_chat_case}}');
        $this->dropTable('{{%client_chat_case}}');
    }
}

<?php

use yii\db\Migration;

/**
 * Class m200721_080306_create_tbl_client_chat_lead
 */
class m200721_080306_create_tbl_client_chat_lead extends Migration
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

        $this->createTable('{{%client_chat_lead}}', [
            'ccl_chat_id' => $this->integer()->notNull(),
            'ccl_lead_id' => $this->integer()->notNull(),
            'ccl_created_dt' => $this->dateTime(),
        ], $tableOptions);

        $this->addPrimaryKey('PK-client_chat_lead-ccl-chat_id-lead_id', '{{%client_chat_lead}}', ['ccl_chat_id', 'ccl_lead_id']);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropPrimaryKey('PK-client_chat_lead-ccl-chat_id-lead_id', '{{%client_chat_lead}}');
        $this->dropTable('{{%client_chat_lead}}');
    }
}

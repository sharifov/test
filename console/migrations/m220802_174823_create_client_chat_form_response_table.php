<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_chat_form_response}}`.
 */
class m220802_174823_create_client_chat_form_response_table extends Migration
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

        $this->createTable('{{%client_chat_form_response}}', [
            '[[ccfr_id]]' => $this->primaryKey(),
            '[[ccfr_uid]]' => $this->string(64)->notNull(),
            '[[ccfr_form_id]]' => $this->integer()->notNull(),
            '[[ccfr_client_chat_id]]' => $this->integer()->notNull(),
            '[[ccfr_value]]' => $this->string(),
            '[[ccfr_rc_created_dt]]' => $this->timestamp(),
            '[[ccfr_created_dt]]' => $this->timestamp()
        ], $tableOptions);

        $this->addForeignKey('FK-client_chat_form_response-ccfr_client_chat_id', '{{%client_chat_form_response}}', '[[ccfr_client_chat_id]]', '{{%client_chat}}', '[[cch_id]]');
        $this->addForeignKey('FK-client_chat_form_response-ccfr_form_id', '{{%client_chat_form_response}}', '[[ccfr_form_id]]', '{{%client_chat_form}}', '[[ccf_id]]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_form_response-ccfr_form_id', '{{%client_chat_form_response}}');
        $this->dropForeignKey('FK-client_chat_form_response-ccfr_client_chat_id', '{{%client_chat_form_response}}');

        $this->dropTable('{{%client_chat_form_response}}');
    }
}

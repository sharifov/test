<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_chat_survey}}`.
 */
class m220419_042912_create_client_chat_survey_table extends Migration
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
        $this->createTable('{{%client_chat_survey}}', [
            '[[ccs_id]]' => $this->primaryKey(),
            '[[ccs_uid]]' => $this->string(64)->notNull(),
            '[[ccs_chat_id]]' => $this->string(64)->notNull(),
            '[[ccs_type]]' => $this->string(64)->notNull(),
            '[[ccs_template]]' => $this->text()->notNull(),
            '[[ccs_trigger_source]]' => $this->string(64)->notNull(),
            '[[ccs_requested_by]]' => $this->integer()->defaultValue(null),
            '[[ccs_requested_for]]' => $this->integer()->notNull(),
            '[[ccs_status]]' => $this->integer()->notNull(),
            '[[ccs_created_dt]]' => $this->timestamp()
        ], $tableOptions);

        $this->addForeignKey('FK-client_chat_survey-ccs_requested_by', '{{%client_chat_survey}}', '[[ccs_requested_by]]', '{{%employees}}', '[[id]]');
        $this->addForeignKey('FK-client_chat_survey-ccs_requested_for', '{{%client_chat_survey}}', '[[ccs_requested_for]]', '{{%employees}}', '[[id]]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_survey-ccs_requested_for', '{{%client_chat_survey}}');
        $this->dropForeignKey('FK-client_chat_survey-ccs_requested_by', '{{%client_chat_survey}}');

        $this->dropTable('{{%client_chat_survey}}');
    }
}

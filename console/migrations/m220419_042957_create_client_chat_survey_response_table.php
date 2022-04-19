<?php

use yii\db\Migration;

/**
 * Handles the creation of table `{{%client_chat_survey_response}}`.
 */
class m220419_042957_create_client_chat_survey_response_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->createTable('{{%client_chat_survey_response}}', [
            '[[ccsr_id]]' => $this->primaryKey(),
            '[[ccsr_client_chat_survey_id]]' => $this->integer()->notNull(),
            '[[ccsr_question]]' => $this->text()->notNull(),
            '[[ccsr_response]]' => $this->text()->notNull()
        ]);

        $this->addForeignKey('FK-client_chat_survey_response-ccsr_client_chat_survey_id', '{{%client_chat_survey_response}}', '[[ccsr_client_chat_survey_id]]', '{{%client_chat_survey}}', '[[ccs_id]]');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-client_chat_survey_response-ccsr_client_chat_survey_id', '{{%client_chat_survey_response}}');
        $this->dropTable('{{%client_chat_survey_response}}');
    }
}

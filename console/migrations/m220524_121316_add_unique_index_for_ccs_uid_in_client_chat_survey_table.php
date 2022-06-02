<?php

use yii\db\Migration;

/**
 * Class m220524_121316_add_unique_index_for_ccs_uid_in_client_chat_survey_table
 */
class m220524_121316_add_unique_index_for_ccs_uid_in_client_chat_survey_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("SET foreign_key_checks = 0;");
        $this->truncateTable('{{%client_chat_survey_response}}');
        $this->truncateTable('{{%client_chat_survey}}');
        $this->createIndex('IND-client_chat_survey-ccs_uid', '{{%client_chat_survey}}', '[[ccs_uid]]', true);
        $this->execute("SET foreign_key_checks = 1;");
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->execute("SET foreign_key_checks = 0;");
        $this->truncateTable('{{%client_chat_survey_response}}');
        $this->truncateTable('{{%client_chat_survey}}');
        $this->dropIndex('IND-client_chat_survey-ccs_uid', '{{%client_chat_survey}}');
        $this->execute("SET foreign_key_checks = 1;");
    }
}

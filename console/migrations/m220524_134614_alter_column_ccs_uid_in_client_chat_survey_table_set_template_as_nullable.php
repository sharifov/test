<?php

use yii\db\Migration;

/**
 * Class m220524_134614_alter_column_ccs_uid_in_client_chat_survey_table_set_template_as_nullable
 */
class m220524_134614_alter_column_ccs_uid_in_client_chat_survey_table_set_template_as_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->execute("SET foreign_key_checks = 0;");
        $this->truncateTable('{{%client_chat_survey_response}}');
        $this->truncateTable('{{%client_chat_survey}}');
        $this->alterColumn('{{%client_chat_survey}}', '[[ccs_template]]', $this->text());
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
        $this->alterColumn('{{%client_chat_survey}}', '[[ccs_template]]', $this->text()->notNull());
        $this->execute("SET foreign_key_checks = 1;");
    }
}

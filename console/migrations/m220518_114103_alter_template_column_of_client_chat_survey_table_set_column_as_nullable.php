<?php

use yii\db\Migration;

/**
 * Class m220518_114103_alter_template_column_of_client_chat_survey_table_set_column_as_nullable
 */
class m220518_114103_alter_template_column_of_client_chat_survey_table_set_column_as_nullable extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%client_chat_survey}}', '[[ccs_template]]', $this->text()->defaultValue(null));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->alterColumn('{{%client_chat_survey}}', '[[ccs_template]]', $this->text()->notNull());
    }
}

<?php

use yii\db\Migration;

/**
 * Class m180822_102508_add_column_additional_information_on_leads_table
 */
class m180822_102508_add_column_additional_information_on_leads_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'additional_information', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'additional_information');
    }
}

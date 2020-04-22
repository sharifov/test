<?php

use yii\db\Migration;

/**
 * Class m181128_084223_add_column_tips_on_leads_table
 */
class m181128_084223_add_column_tips_on_leads_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'tips', $this->decimal(10,2)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'tips');
    }
}

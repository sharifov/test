<?php

use yii\db\Migration;

/**
 * Class m180815_075801_add_columns_on_leads_table
 */
class m180815_075801_add_columns_on_leads_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'called_expert', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'called_expert');
    }
}

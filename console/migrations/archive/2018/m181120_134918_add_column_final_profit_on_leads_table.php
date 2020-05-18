<?php

use yii\db\Migration;

/**
 * Class m181120_134918_add_column_final_profit_on_leads_table
 */
class m181120_134918_add_column_final_profit_on_leads_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'final_profit', $this->float(2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'final_profit');
    }
}

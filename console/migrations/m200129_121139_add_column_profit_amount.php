<?php

use yii\db\Migration;

/**
 * Class m200129_121139_add_column_profit_amount
 */
class m200129_121139_add_column_profit_amount extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote}}', 'pq_profit_amount', $this->decimal(8,2)->defaultValue(0));
        $this->addColumn('{{%offer}}', 'of_profit_amount', $this->decimal(8,2)->defaultValue(0));
        $this->addColumn('{{%order}}', 'or_profit_amount', $this->decimal(8,2)->defaultValue(0));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote}}', 'pq_profit_amount');
        $this->dropColumn('{{%offer}}', 'of_profit_amount');
        $this->dropColumn('{{%order}}', 'or_profit_amount');
    }
}

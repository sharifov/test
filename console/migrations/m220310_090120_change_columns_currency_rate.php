<?php

use yii\db\Migration;

/**
 * Class m220310_090120_change_columns_currency_rate
 */
class m220310_090120_change_columns_currency_rate extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%currency}}', 'cur_base_rate', $this->decimal(14, 5)->defaultValue(1));
        $this->alterColumn('{{%currency}}', 'cur_app_rate', $this->decimal(14, 5)->defaultValue(1));
        $this->alterColumn('{{%currency}}', 'cur_symbol', $this->string(10)->notNull());

        $this->alterColumn('{{%order}}', 'or_client_currency_rate', $this->decimal(14, 5));

        $this->alterColumn('{{%product_quote}}', 'pq_origin_currency_rate', $this->decimal(14, 5));
        $this->alterColumn('{{%product_quote}}', 'pq_client_currency_rate', $this->decimal(14, 5));

        $this->alterColumn('{{%offer}}', 'of_client_currency_rate', $this->decimal(14, 5));

        $this->alterColumn('{{%quotes}}', 'q_client_currency_rate', $this->decimal(14, 5)->defaultValue(1));

        $this->alterColumn('{{%invoice}}', 'inv_currency_rate', $this->decimal(14, 5));

        $this->alterColumn('{{%currency_history}}', 'ch_base_rate', $this->decimal(14, 5)->defaultValue(1));
        $this->alterColumn('{{%currency_history}}', 'ch_app_rate', $this->decimal(14, 5)->defaultValue(1));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220310_090120_change_columns_currency_rate cannot be reverted.\n";
    }
}

<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211109_152136_add_columns_to_product_quote_change
 */
class m211109_152136_add_columns_to_product_quote_change extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_change}}', 'pqc_penalty_amount', $this->decimal(8, 2));
        $this->addColumn('{{%product_quote_change}}', 'pqc_client_penalty_amount', $this->decimal(8, 2));
        $this->addColumn('{{%product_quote_change}}', 'pqc_client_currency_rate', $this->decimal(8, 2));
        $this->addColumn('{{%product_quote_change}}', 'pqc_client_currency', $this->string(3));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_change}}', 'pqc_penalty_amount');
        $this->dropColumn('{{%product_quote_change}}', 'pqc_client_penalty_amount');
        $this->dropColumn('{{%product_quote_change}}', 'pqc_client_currency_rate');
        $this->dropColumn('{{%product_quote_change}}', 'pqc_client_currency');
    }
}

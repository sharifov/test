<?php

use common\models\Currency;
use yii\db\Migration;

/**
 * Class m220221_123413_add_columns_client_price
 */
class m220221_123413_add_columns_client_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%quote_price}}', 'qp_client_fare', $this->decimal(10, 2));
        $this->addColumn('{{%quote_price}}', 'qp_client_taxes', $this->decimal(10, 2));
        $this->addColumn('{{%quote_price}}', 'qp_client_markup', $this->decimal(10, 2));

        $this->addColumn(
            '{{%quotes}}',
            'q_client_currency',
            $this->string(3)->defaultValue(Currency::DEFAULT_CURRENCY)
        );
        $this->addColumn(
            '{{%quotes}}',
            'q_client_currency_rate',
            $this->decimal(8, 5)->defaultValue(1)
        );

        $this->createIndex('IND-quotes-q_client_currency', '{{%quotes}}', 'q_client_currency');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-quotes-q_client_currency', '{{%quotes}}');

        $this->dropColumn('{{%quote_price}}', 'qp_client_fare');
        $this->dropColumn('{{%quote_price}}', 'qp_client_taxes');
        $this->dropColumn('{{%quote_price}}', 'qp_client_markup');

        $this->dropColumn('{{%quotes}}', 'q_client_currency');
        $this->dropColumn('{{%quotes}}', 'q_client_currency_rate');
    }
}

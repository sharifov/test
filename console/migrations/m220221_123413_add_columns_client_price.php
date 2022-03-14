<?php

use common\models\Currency;
use src\helpers\app\AppHelper;
use src\helpers\app\DBHelper;
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
        if (!DBHelper::isColumnExist('quote_price', 'qp_client_fare')) {
            $this->addColumn('{{%quote_price}}', 'qp_client_fare', $this->decimal(10, 2));
        }
        if (!DBHelper::isColumnExist('quote_price', 'qp_client_taxes')) {
            $this->addColumn('{{%quote_price}}', 'qp_client_taxes', $this->decimal(10, 2));
        }
        if (!DBHelper::isColumnExist('quote_price', 'qp_client_markup')) {
            $this->addColumn('{{%quote_price}}', 'qp_client_markup', $this->decimal(10, 2));
        }

        if (!DBHelper::isColumnExist('quote_price', 'q_client_currency')) {
            $this->addColumn(
                '{{%quotes}}',
                'q_client_currency',
                $this->string(3)->defaultValue(Currency::DEFAULT_CURRENCY)
            );
        }
        if (!DBHelper::isColumnExist('quote_price', 'q_client_currency_rate')) {
            $this->addColumn(
                '{{%quotes}}',
                'q_client_currency_rate',
                $this->decimal(8, 5)->defaultValue(1)
            );
        }

        try {
            if (!DBHelper::isIndexExist('quotes', 'IND-quotes-q_client_currency')) {
                $this->createIndex('IND-quotes-q_client_currency', '{{%quotes}}', 'q_client_currency');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220221_123413_add_columns_client_price:safeUp');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        try {
            if (DBHelper::isIndexExist('quotes', 'IND-quotes-q_client_currency')) {
                $this->dropIndex('IND-quotes-q_client_currency', '{{%quotes}}');
            }
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'm220221_123413_add_columns_client_price:safeDown');
        }


        if (DBHelper::isColumnExist('quote_price', 'qp_client_fare')) {
            $this->dropColumn('{{%quote_price}}', 'qp_client_fare');
        }
        if (DBHelper::isColumnExist('quote_price', 'qp_client_taxes')) {
            $this->dropColumn('{{%quote_price}}', 'qp_client_taxes');
        }
        if (DBHelper::isColumnExist('quote_price', 'qp_client_markup')) {
            $this->dropColumn('{{%quote_price}}', 'qp_client_markup');
        }

        if (DBHelper::isColumnExist('quotes', 'q_client_currency')) {
            $this->dropColumn('{{%quotes}}', 'q_client_currency');
        }
        if (DBHelper::isColumnExist('quotes', 'q_client_currency_rate')) {
            $this->dropColumn('{{%quotes}}', 'q_client_currency_rate');
        }
    }
}

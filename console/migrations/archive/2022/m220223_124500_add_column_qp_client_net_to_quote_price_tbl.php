<?php

use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220223_124500_add_column_qp_client_net_to_quote_price_tbl
 */
class m220223_124500_add_column_qp_client_net_to_quote_price_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!DBHelper::isColumnExist('quote_price', 'qp_client_net')) {
            $this->addColumn('{{%quote_price}}', 'qp_client_net', $this->decimal(10, 2));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (DBHelper::isColumnExist('quote_price', 'qp_client_net')) {
            $this->dropColumn('{{%quote_price}}', 'qp_client_net');
        }
    }
}

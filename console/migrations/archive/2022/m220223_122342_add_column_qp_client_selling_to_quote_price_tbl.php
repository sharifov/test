<?php

use src\helpers\app\DBHelper;
use yii\db\Migration;

/**
 * Class m220223_122342_add_column_qp_client_selling_to_quote_price_tbl
 */
class m220223_122342_add_column_qp_client_selling_to_quote_price_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        if (!DBHelper::isColumnExist('quote_price', 'qp_client_selling')) {
            $this->addColumn('{{%quote_price}}', 'qp_client_selling', $this->decimal(10, 2));
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        if (DBHelper::isColumnExist('quote_price', 'qp_client_selling')) {
            $this->dropColumn('{{%quote_price}}', 'qp_client_selling');
        }
    }
}

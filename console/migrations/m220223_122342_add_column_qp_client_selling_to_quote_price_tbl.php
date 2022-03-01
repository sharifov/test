<?php

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
        $this->addColumn('{{%quote_price}}', 'qp_client_selling', $this->decimal(10, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%quote_price}}', 'qp_client_selling');
    }
}

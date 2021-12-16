<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211025_073623_add_column_to_tbl_product_quote_refund
 */
class m211025_073623_add_column_to_tbl_product_quote_refund extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_refund}}', 'pqr_data_json', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_data_json');
    }
}

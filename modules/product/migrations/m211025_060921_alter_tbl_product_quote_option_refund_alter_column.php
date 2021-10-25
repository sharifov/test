<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211025_060921_alter_tbl_product_quote_option_refund_alter_column
 */
class m211025_060921_alter_tbl_product_quote_option_refund_alter_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->renameColumn('product_quote_option_refund', 'pqor_details', 'pqor_data_json');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->renameColumn('product_quote_option_refund', 'pqor_data_json', 'pqor_details');
    }
}

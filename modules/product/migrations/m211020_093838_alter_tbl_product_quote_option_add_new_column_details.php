<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211020_093838_alter_tbl_product_quote_option_add_new_column_details
 */
class m211020_093838_alter_tbl_product_quote_option_add_new_column_details extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_option_refund}}', 'pqor_details', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_option_refund}}', 'pqor_details');
    }
}

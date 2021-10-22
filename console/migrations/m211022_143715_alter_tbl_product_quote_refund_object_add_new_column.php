<?php

use yii\db\Migration;

/**
 * Class m211022_143715_alter_tbl_product_quote_refund_object_add_new_column
 */
class m211022_143715_alter_tbl_product_quote_refund_object_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_object_refund}}', 'pqor_data_json', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_object_refund}}', 'pqor_data_json');
    }
}

<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211025_060645_alter_tbl_product_quote_refund_object_add_new_column
 */
class m211025_060645_alter_tbl_product_quote_refund_object_add_new_column extends Migration
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

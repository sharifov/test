<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211018_144007_alter_tbl_product_quote_refund_add_column
 */
class m211018_144007_alter_tbl_product_quote_refund_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_refund}}', 'pqr_type_id', $this->tinyInteger()->unsigned());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_type_id');
    }
}

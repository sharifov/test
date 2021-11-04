<?php

namespace modules\product\migrations;

use modules\product\src\entities\productQuoteRefund\ProductQuoteRefund;
use yii\db\Migration;

/**
 * Class m211025_101239_alter_tbl_product_quote_refund_add_new_column
 */
class m211025_101239_alter_tbl_product_quote_refund_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $schema = $this->db->getTableSchema('product_quote_refund');
        if ($schema && !in_array('pqr_gid', $schema->columnNames, true)) {
            $this->addColumn('{{%product_quote_refund}}', 'pqr_gid', $this->string(32)->notNull()->after('pqr_id'));
            $this->createIndex('IND-product_quote_refund-pqr_gid', '{{%product_quote_refund}}', 'pqr_gid');
        }

        $productQuoteRefunds = ProductQuoteRefund::find()->all();
        foreach ($productQuoteRefunds as $productQuoteRefund) {
            $productQuoteRefund->pqr_gid = ProductQuoteRefund::generateGid();
            $productQuoteRefund->save();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_gid');
    }
}

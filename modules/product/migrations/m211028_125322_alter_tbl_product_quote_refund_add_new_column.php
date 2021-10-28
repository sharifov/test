<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211028_125322_alter_tbl_product_quote_refund_add_new_column
 */
class m211028_125322_alter_tbl_product_quote_refund_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_refund}}', 'pqr_cid', $this->string(32));
        $this->createIndex('IND-product_quote_refund-pqr_cid', '{{%product_quote_refund}}', 'pqr_cid');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_refund}}', 'pqr_cid');
    }
}

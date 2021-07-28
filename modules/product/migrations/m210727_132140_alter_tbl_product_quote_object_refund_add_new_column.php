<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m210727_132140_alter_tbl_product_quote_object_refund_add_new_column
 */
class m210727_132140_alter_tbl_product_quote_object_refund_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_object_refund}}', 'pqor_quote_object_id', $this->integer()->notNull()->after('pqor_product_quote_refund_id'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_object_refund}}', 'pqor_quote_object_id');
    }
}

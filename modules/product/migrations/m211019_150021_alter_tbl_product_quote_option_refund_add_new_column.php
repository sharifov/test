<?php

namespace modules\product\migrations;

use yii\db\Migration;

/**
 * Class m211019_150021_alter_tbl_product_quote_option_refund_add_new_column
 */
class m211019_150021_alter_tbl_product_quote_option_refund_add_new_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%product_quote_option_refund}}', 'pqor_refund_allow', $this->boolean()->after('pqor_client_refund_amount'));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%product_quote_option_refund}}', 'pqor_refund_allow');
    }
}

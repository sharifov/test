<?php

use yii\db\Migration;

/**
 * Class m200522_090007_add_columns_tbl_sale_ticket
 */
class m200522_090007_add_columns_tbl_sale_ticket extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%sale_ticket}}', 'st_transaction_ids', $this->text());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%sale_ticket}}', 'st_transaction_ids');
    }
}

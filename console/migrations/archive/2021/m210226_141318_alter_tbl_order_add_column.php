<?php

use yii\db\Migration;

/**
 * Class m210226_141318_alter_tbl_order_add_column
 */
class m210226_141318_alter_tbl_order_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'or_request_data', $this->json());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'or_request_data');
    }
}

<?php

use yii\db\Migration;

/**
 * Class m210721_094806_add_column_or_sale_id
 */
class m210721_094806_add_column_or_sale_id extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%order}}', 'or_sale_id', $this->integer());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%order}}', 'or_sale_id');
    }
}

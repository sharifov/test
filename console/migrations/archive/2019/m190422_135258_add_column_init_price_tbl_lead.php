<?php

use yii\db\Migration;

/**
 * Class m190422_135258_add_column_init_price_tbl_lead
 */
class m190422_135258_add_column_init_price_tbl_lead extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%leads}}', 'l_init_price', $this->decimal(10, 2));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%leads}}', 'l_init_price');
    }

}

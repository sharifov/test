<?php

use yii\db\Migration;

/**
 * Class m191127_153711_create_fk_tbl_product
 */
class m191127_153711_create_fk_tbl_product extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addForeignKey('FK-product-pr_type_id', '{{%product}}', ['pr_type_id'], '{{%product_type}}', ['pt_id'], 'CASCADE', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m191127_153711_create_fk_tbl_product cannot be reverted.\n";

        return false;
    }

}

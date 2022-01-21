<?php

use yii\db\Migration;

/**
 * Class m210302_145735_alter_tbl_billing_info_add_foreign_key
 */
class m210302_145735_alter_tbl_billing_info_add_foreign_key extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%billing_info}}', 'bi_payment_method_id', $this->integer());
        $this->addForeignKey('FK-billing_info-bi_payment_method_id', '{{%billing_info}}', 'bi_payment_method_id', '{{%payment_method}}', 'pm_id', 'SET NULL', 'CASCADE');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropForeignKey('FK-billing_info-bi_payment_method_id', '{{%billing_info}}');
    }
}

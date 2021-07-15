<?php

use yii\db\Migration;

/**
 * Class m210715_062422_add_column_c_used_count
 */
class m210715_062422_add_column_c_used_count extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%coupon}}', 'c_used_count', $this->integer()->notNull()->defaultValue(0));

        $this->createIndex('IND-coupon-c_used_count', '{{%coupon}}', 'c_used_count');
        $this->createIndex('IND-coupon-c_disabled', '{{%coupon}}', 'c_disabled');
        $this->createIndex('IND-coupon-c_reusable', '{{%coupon}}', 'c_reusable');
        $this->createIndex('IND-coupon-c_reusable_count', '{{%coupon}}', 'c_reusable_count');
        $this->createIndex('IND-coupon-c_start_date', '{{%coupon}}', 'c_start_date');
        $this->createIndex('IND-coupon-c_exp_date', '{{%coupon}}', 'c_exp_date');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropIndex('IND-coupon-c_used_count', '{{%coupon}}');
        $this->dropIndex('IND-coupon-c_disabled', '{{%coupon}}');
        $this->dropIndex('IND-coupon-c_reusable', '{{%coupon}}');
        $this->dropIndex('IND-coupon-c_reusable_count', '{{%coupon}}');
        $this->dropIndex('IND-coupon-c_start_date', '{{%coupon}}');
        $this->dropIndex('IND-coupon-c_exp_date', '{{%coupon}}');

        $this->dropColumn('{{%coupon}}', 'c_used_count');
    }
}

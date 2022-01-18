<?php

use yii\db\Migration;

/**
 * Class m210713_092632_drop_user_fk_from_coupone_tbl
 */
class m210713_092632_drop_user_fk_from_coupone_tbl extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->dropForeignKey('FK-coupon-c_created_user_id', '{{%coupon}}');
        $this->dropForeignKey('FK-coupon-c_updated_user_id', '{{%coupon}}');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addForeignKey('FK-coupon-c_created_user_id', '{{%coupon}}', ['c_created_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
        $this->addForeignKey('FK-coupon-c_updated_user_id', '{{%coupon}}', ['c_updated_user_id'], '{{%employees}}', ['id'], 'SET NULL', 'CASCADE');
    }
}

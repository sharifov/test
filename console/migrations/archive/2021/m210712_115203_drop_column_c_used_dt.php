<?php

use yii\db\Migration;

/**
 * Class m210712_115203_drop_column_c_used_dt
 */
class m210712_115203_drop_column_c_used_dt extends Migration
{
    public function safeUp()
    {
        $this->dropColumn('{{%coupon}}', 'c_used_dt');
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->addColumn('{{%coupon}}', 'c_used_dt', $this->dateTime());
    }
}

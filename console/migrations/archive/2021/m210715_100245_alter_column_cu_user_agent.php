<?php

use yii\db\Migration;

/**
 * Class m210715_100245_alter_column_cu_user_agent
 */
class m210715_100245_alter_column_cu_user_agent extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%coupon_use}}', 'cu_user_agent', $this->string(500));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210715_100245_alter_column_cu_user_agent cannot be reverted.\n";
    }
}

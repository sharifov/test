<?php

use yii\db\Migration;

/**
 * Class m210810_090340_alter_column_cs_order_uid
 */
class m210810_090340_alter_column_cs_order_uid extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%cases}}', 'cs_order_uid', $this->string(10));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210810_090340_alter_column_cs_order_uid cannot be reverted.\n";
    }
}

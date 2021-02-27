<?php

use yii\db\Migration;

/**
 * Class m210227_072840_alter_tbl_payment_add_column
 */
class m210227_072840_alter_tbl_payment_add_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment_method}}', 'pm_key', $this->string(50));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment_method}}', 'pm_key');
    }
}

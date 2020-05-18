<?php

use yii\db\Migration;

/**
 * Class m190206_144948_alter_add_column_call_price
 */
class m190206_144948_alter_add_column_call_price extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_price', $this->decimal(10, 5));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%call}}', 'c_price');
    }
}

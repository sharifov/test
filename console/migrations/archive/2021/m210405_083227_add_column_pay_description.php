<?php

use yii\db\Migration;

/**
 * Class m210405_083227_add_column_pay_description
 */
class m210405_083227_add_column_pay_description extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%payment}}', 'pay_description', $this->string(255));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%payment}}', 'pay_description');
    }
}

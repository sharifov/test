<?php

use yii\db\Migration;

/**
 * Class m210308_101343_alter_column_s_value
 */
class m210308_101343_alter_column_s_value extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%setting}}', 's_value', $this->string(700));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m210308_101343_alter_column_s_value cannot be reverted.\n";
    }
}

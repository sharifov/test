<?php

use yii\db\Migration;

/**
 * Class m220311_070101_alter_column_gb_log
 */
class m220311_070101_alter_column_gb_log extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->alterColumn('{{%global_log}}', 'gl_model', $this->string(100)->notNull());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220311_070101_alter_column_gb_log cannot be reverted.\n";
    }
}

<?php

use yii\db\Migration;

/**
 * Class m220311_103552_modyfy_setting_table_expand_s_value_column
 */
class m220311_103552_modyfy_setting_table_expand_s_value_column extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        try {
            $this->alterColumn('{{%setting}}', 's_value', $this->string(5000));
        } catch (Throwable $throwable) {
            Yii::error(
                $throwable,
                'm220311_103552_modyfy_setting_table_expand_s_value_column:safeUp:Throwable'
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        echo "m220311_103552_modyfy_setting_table_expand_s_value_column cannot be reverted.\n";

        return false;
    }
}

<?php

use yii\db\Migration;

/**
 * Class m210705_102707_create_s_description_column_in_setting_table
 */
class m210705_102707_create_s_description_column_in_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%setting}}', 's_description', $this->string(1000)->null());
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%setting}}', 's_description');
    }
}

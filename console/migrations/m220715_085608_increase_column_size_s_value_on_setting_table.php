<?php

use yii\db\Migration;

/**
 * Class m220715_085608_increase_column_size_s_value_on_setting_table
 */
class m220715_085608_increase_column_size_s_value_on_setting_table extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->db->createCommand("ALTER TABLE setting MODIFY s_value VARCHAR(7000)")->execute();
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->db->createCommand('ALTER TABLE setting MODIFY s_value VARCHAR(6000)')->execute();
    }
}

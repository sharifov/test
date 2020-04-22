<?php

use yii\db\Migration;

/**
 * Class m190606_084256_table_setting_add_settings_data_col
 */
class m190606_084256_table_setting_add_settings_data_col extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'direct_agent_user_limit',
            's_name' => 'Direct agent call user limit',
            's_type' => 'int',
            's_value' => 3,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'direct_agent_user_limit'
        ]]);
    }
}

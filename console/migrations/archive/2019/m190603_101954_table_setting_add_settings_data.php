<?php

use yii\db\Migration;

/**
 * Class m190603_101954_table_setting_add_settings_data
 */
class m190603_101954_table_setting_add_settings_data extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'use_general_line_distribution',
            's_name' => 'Use general line distribution',
            's_type' => 'bool',
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'general_line_leads_limit',
            's_name' => 'General line leads limit',
            's_type' => 'int',
            's_value' => 10,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);
        $this->insert('{{%setting}}', [
            's_key' => 'general_line_role_priority',
            's_name' => 'General line role priority',
            's_type' => 'bool',
            's_value' => 1,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);
        $this->insert('{{%setting}}', [
            's_key' => 'general_line_last_hours',
            's_name' => 'General line last hours',
            's_type' => 'int',
            's_value' => 12,
            's_updated_dt' => date('Y-m-d H:i:s'),
            's_updated_user_id' => 1,
        ]);
        $this->insert('{{%setting}}', [
            's_key' => 'general_line_user_limit',
            's_name' => 'General line user limit',
            's_type' => 'int',
            's_value' => 10,
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
            'use_general_line_distribution', 'general_line_leads_limit', 'general_line_role_priority', 'general_line_last_hours', 'general_line_user_limit'
        ]]);
    }
}

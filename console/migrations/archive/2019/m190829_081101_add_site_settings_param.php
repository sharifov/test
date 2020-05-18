<?php

use yii\db\Migration;

/**
 * Class m190829_081101_add_site_settings_param
 */
class m190829_081101_add_site_settings_param extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'time_start_call_user_access_general',
            's_name' => 'The time after which starts Call user access - General Line (seconds)',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 15,
            's_updated_dt' => date('Y-m-d H:i:s'),
            //'s_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'time_start_call_user_access_direct',
            's_name' => 'The time after which starts Call user access - Direct Line (seconds)',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 60,
            's_updated_dt' => date('Y-m-d H:i:s'),
            //'s_updated_user_id' => 1,
        ]);

        $this->insert('{{%setting}}', [
            's_key' => 'time_repeat_call_user_access',
            's_name' => 'The repeat time for Call user access check (seconds)',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 20,
            's_updated_dt' => date('Y-m-d H:i:s'),
            //'s_updated_user_id' => 1,
        ]);


    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'time_start_call_user_access',
            'time_start_call_user_access_direct',
            'time_start_call_user_access_general',
            'time_repeat_call_user_access'
        ]]);
    }
}

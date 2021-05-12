<?php

use yii\db\Migration;

/**
 * Class m210507_132418_add_setting_client_chat_user_access_history_days
 */
class m210507_132418_add_setting_client_chat_user_access_history_days extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->insert('{{%setting}}', [
            's_key' => 'client_chat_user_access_history_days',
            's_name' => 'Client Chat User Access History (days)',
            's_type' => \common\models\Setting::TYPE_INT,
            's_value' => 5,
            's_updated_dt' => date('Y-m-d H:i:s'),
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_chat_user_access_history_days'
        ]]);
    }
}

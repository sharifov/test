<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m211209_161425_add_call_reconnect_announce_message
 */
class m211209_161425_add_call_reconnect_announce_message extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Call');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_reconnect_announce',
                's_name' => 'Call reconnect announce message',
                's_type' => Setting::TYPE_STRING,
                's_value' => 'Connection Error. Reconnecting. Please hold',
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => null,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_reconnect_announce',
        ]]);
    }
}

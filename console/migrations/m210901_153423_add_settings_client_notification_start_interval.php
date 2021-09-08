<?php

use common\models\Setting;
use common\models\SettingCategory;
use frontend\helpers\JsonHelper;
use yii\db\Migration;

/**
 * Class m210901_153423_add_settings_client_notification_start_interval
 */
class m210901_153423_add_settings_client_notification_start_interval extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Client notifications');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_notification_start_interval',
                's_name' => 'Client notification start interval',
                's_type' => Setting::TYPE_ARRAY,
                's_value' => JsonHelper::encode([
                    'days' => 0,
                    'hours' => 0,
                ]),
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_notification_start_interval',
        ]]);
    }
}

<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210901_080257_add_settings_client_notifications_history_days
 */
class m210901_080257_add_settings_client_notifications_history_days extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Cleaner');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'client_notifications_history_days',
                's_name' => 'Client Notifications history days',
                's_type' => Setting::TYPE_INT,
                's_value' => 30,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Client notifications older than the specified days will be automatically deleted',
            ]
        );

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'client_notifications_history_days'
        ]]);
    }
}

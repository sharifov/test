<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210729_114559_add_setting_notifications_history_days
 */
class m210729_114559_add_setting_notifications_history_days extends Migration
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
                's_key' => 'notifications_history_days',
                's_name' => 'Notifications history days',
                's_type' => Setting::TYPE_INT,
                's_value' => 60,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Notifications older than the specified days will be automatically deleted',
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
            'notifications_history_days'
        ]]);
    }
}

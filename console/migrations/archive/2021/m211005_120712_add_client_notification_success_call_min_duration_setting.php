<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m211005_120712_add_client_notification_success_call_min_duration_setting
 */
class m211005_120712_add_client_notification_success_call_min_duration_setting extends Migration
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
                's_key' => 'client_notification_success_call_min_duration',
                's_name' => 'Client notification success call min duration',
                's_type' => Setting::TYPE_INT,
                's_value' => 30,
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
            'client_notification_success_call_min_duration',
        ]]);
    }
}

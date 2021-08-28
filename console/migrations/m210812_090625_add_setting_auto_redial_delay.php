<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210812_090625_add_setting_auto_redial_delay
 */
class m210812_090625_add_setting_auto_redial_delay extends Migration
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
                's_key' => 'call_lead_auto_redial_delay',
                's_name' => 'Lead auto Redial delay (sec)',
                's_type' => Setting::TYPE_INT,
                's_value' => 0,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'For logic Lead auto redial (in seconds)',
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_lead_auto_redial_enabled',
                's_name' => 'Lead auto Redial enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => 0,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Enable Lead auto redial',
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
            'call_lead_auto_redial_delay', 'call_lead_auto_redial_enabled'
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}

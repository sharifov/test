<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m220125_124804_add_setting_clean_lead_poor_processing_log_after_days
 */
class m220125_124804_add_setting_clean_lead_poor_processing_log_after_days extends Migration
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
                's_key' => 'clean_lead_poor_processing_log_after_days',
                's_name' => 'Clean Lead Poor Processing Log after days',
                's_type' => Setting::TYPE_INT,
                's_value' => 90,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Lead Poor Processing Log older than the specified days will be automatically deleted',
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
            'clean_lead_poor_processing_log_after_days'
        ]]);
    }
}

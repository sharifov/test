<?php

use yii\db\Migration;

/**
 * Class m211007_092934_add_settings_leads_travel_dates_passed_trashed_interval
 */
class m211007_092934_add_settings_leads_travel_dates_passed_trashed_interval extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Cleaner');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'leads_travel_dates_passed_trashed_hours',
                's_name' => 'Auto trash leads with Travel Dates Passed hours',
                's_type' => \common\models\Setting::TYPE_INT,
                's_value' => 24,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Leads with Travel Dates Passed older than the specified hours will be automatically trashed',
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
            'leads_travel_dates_passed_trashed_hours'
        ]]);
    }
}

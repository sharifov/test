<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210617_073502_add_new_site_setting_for_case_queues
 */
class m210617_073502_add_new_site_setting_for_case_queues extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Cases');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'case_past_departure_date',
                's_name' => 'Case Past Departure Date',
                's_type' => Setting::TYPE_INT,
                's_value' => 2,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'case_priority_days',
                's_name' => 'Case Priority Days',
                's_type' => Setting::TYPE_INT,
                's_value' => 14,
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
            'case_past_departure_date',
            'case_priority_days'
        ]]);
    }
}

<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210528_051455_add_setting_job_execution_time
 */
class m210528_051455_add_setting_job_execution_time extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = SettingCategory::getOrCreateByName('Metric');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'metric_job_time_execution',
                's_name' => 'Metric Job log limit execution. If 0 - disable',
                's_type' => Setting::TYPE_INT,
                's_value' => 0,
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
            'metric_job_time_execution',
        ]]);
    }
}

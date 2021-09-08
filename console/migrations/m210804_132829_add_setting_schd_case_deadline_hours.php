<?php

use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;

/**
 * Class m210804_132829_add_setting_schd_case_deadline_hours
 */
class m210804_132829_add_setting_schd_case_deadline_hours extends Migration
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
                's_key' => 'schd_case_deadline_hours',
                's_name' => 'Schedule case deadline hours',
                's_type' => Setting::TYPE_INT,
                's_value' => 0,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
                's_description' => 'Case Deadline - depends on Site Settings: schd_case_deadline_hours. Case Deadline = departure date for the first segment of the original quota minus schd_case_deadline_hours.',
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
            'schd_case_deadline_hours',
        ]]);

        if (Yii::$app->cache) {
            Yii::$app->cache->flush();
        }
    }
}

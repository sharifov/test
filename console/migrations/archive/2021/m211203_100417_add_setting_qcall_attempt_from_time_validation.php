<?php

use yii\db\Migration;

/**
 * Class m211203_100417_add_setting_qcall_attempt_from_time_validation
 */
class m211203_100417_add_setting_qcall_attempt_from_time_validation extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $settingCategory = \common\models\SettingCategory::getOrCreateByName('Redial');

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'lead_redial_qcall_attempts_from_time_validation_enabled',
                's_name' => 'Date From call attempt validation',
                's_type' => \common\models\Setting::TYPE_BOOL,
                's_value' => true,
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
            'lead_redial_qcall_attempts_from_time_validation_enabled',
        ]]);
    }
}

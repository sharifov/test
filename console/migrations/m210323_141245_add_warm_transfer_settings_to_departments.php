<?php

use common\models\Department;
use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m210323_141245_add_warm_transfer_settings_to_departments
 */
class m210323_141245_add_warm_transfer_settings_to_departments extends Migration
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
                's_key' => 'warm_transfer_timeout',
                's_name' => 'Warm transfer timeout',
                's_type' => Setting::TYPE_INT,
                's_value' => 30,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'warm_transfer_auto_unhold_enabled',
                's_name' => 'Warm transfer auto un-hold enabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        foreach (Department::find()->all() as $department) {
            $customData = [];
            /** @var Department $department */
            if ($department->dep_params) {
                $customData = @json_decode($department->dep_params, true);
                if (!$customData) {
                    $customData = [];
                }
            }
            $customData['warm_transfer'] = [
                'timeout' => null,
                'auto_unhold_enabled' => null,
            ];
            $department->dep_params = @json_encode($customData);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->delete('{{%setting}}', ['IN', 's_key', [
            'warm_transfer_timeout',
            'warm_transfer_auto_unhold_enabled'
        ]]);

        foreach (Department::find()->all() as $department) {
            /** @var Department $department */
            if (!$department->dep_params) {
                continue;
            }
            $customData = @json_decode($department->dep_params, true);
            if (!$customData) {
                continue;
            }
            if (!array_key_exists('warm_transfer', $customData)) {
                continue;
            }
            unset($customData['warm_transfer']);
            $department->dep_params = @json_encode($customData);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }
    }
}

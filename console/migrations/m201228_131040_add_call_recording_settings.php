<?php

use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Project;
use common\models\Setting;
use common\models\SettingCategory;
use yii\db\Migration;
use yii\helpers\VarDumper;

/**
 * Class m201228_131040_add_call_recording_settings
 */
class m201228_131040_add_call_recording_settings extends Migration
{
    /**
     * {@inheritdoc}
     */
    public function safeUp()
    {
        $this->addColumn('{{%call}}', 'c_recording_disabled', $this->boolean()->defaultValue(false));

        $settingCategory = SettingCategory::getOrCreateByName('Cleaner');
        $this->insert(
            '{{%setting}}',
            [
                's_key' => 'call_recording_disabled',
                's_name' => 'Call recording disabled',
                's_type' => Setting::TYPE_BOOL,
                's_value' => false,
                's_updated_dt' => date('Y-m-d H:i:s'),
                's_category_id' => $settingCategory->sc_id,
            ]
        );

        $this->addColumn('{{%user_profile}}', 'up_call_recording_disabled', $this->boolean()->defaultValue(false));

        foreach (Project::find()->all() as $project) {
            $customData = [];
            /** @var Project $project */
            if ($project->custom_data) {
                $customData = json_decode($project->custom_data, true, 512, JSON_THROW_ON_ERROR);
            }
            $customData['call_recording_disabled'] = false;
            $project->custom_data = @json_encode($customData);
            if (!$project->save()) {
                VarDumper::dump($project->getErrors());
            }
        }

        foreach (Department::find()->all() as $department) {
            $customData = [];
            /** @var Department $department */
            if ($department->dep_params) {
                $customData = @json_decode($department->dep_params, true, 512, JSON_THROW_ON_ERROR);
            }
            $customData['call_recording_disabled'] = false;
            $department->dep_params = @json_encode($customData);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }

        foreach (DepartmentPhoneProject::find()->all() as $phone) {
            $customData = [];
            /** @var DepartmentPhoneProject $phone */
            if ($phone->dpp_params) {
                $customData = @json_decode($phone->dpp_params, true, 512, JSON_THROW_ON_ERROR);
            }
            $customData['call_recording_disabled'] = false;
            $phone->dpp_params = @json_encode($customData);
            if (!$phone->save()) {
                VarDumper::dump($phone->getErrors());
            }
        }

        $this->addColumn('{{%clients}}', 'cl_call_recording_disabled', $this->boolean()->defaultValue(false));
    }

    /**
     * {@inheritdoc}
     */
    public function safeDown()
    {
        $this->dropColumn('{{%clients}}', 'cl_call_recording_disabled');

        foreach (DepartmentPhoneProject::find()->all() as $phone) {
            /** @var DepartmentPhoneProject $phone */
            if (!$phone->dpp_params) {
                continue;
            }
            $customData = @json_decode($phone->dpp_params, true, 512, JSON_THROW_ON_ERROR);
            if (!array_key_exists('call_recording_disabled', $customData)) {
                continue;
            }
            unset($customData['call_recording_disabled']);
            $phone->dpp_params = @json_encode($customData);
            if (!$phone->save()) {
                VarDumper::dump($phone->getErrors());
            }
        }

        foreach (Department::find()->all() as $department) {
            /** @var Department $department */
            if (!$department->dep_params) {
                continue;
            }
            $customData = @json_decode($department->dep_params, true, 512, JSON_THROW_ON_ERROR);
            if (!array_key_exists('call_recording_disabled', $customData)) {
                continue;
            }
            unset($customData['call_recording_disabled']);
            $department->dep_params = @json_encode($customData);
            if (!$department->save()) {
                VarDumper::dump($department->getErrors());
            }
        }

        foreach (Project::find()->all() as $project) {
            /** @var Project $project */
            if (!$project->custom_data) {
                continue;
            }
            $customData = @json_decode($project->custom_data, true, 512, JSON_THROW_ON_ERROR);
            if (!array_key_exists('call_recording_disabled', $customData)) {
                continue;
            }
            unset($customData['call_recording_disabled']);
            $project->custom_data = @json_encode($customData);
            if (!$project->save()) {
                VarDumper::dump($project->getErrors());
            }
        }

        $this->dropColumn('{{%user_profile}}', 'up_call_recording_disabled');

        $this->delete('{{%setting}}', ['IN', 's_key', [
            'call_recording_disabled'
        ]]);

        $this->dropColumn('{{%call}}', 'c_recording_disabled');
    }
}

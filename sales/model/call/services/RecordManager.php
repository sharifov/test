<?php

namespace sales\model\call\services;

use common\models\Client;
use common\models\Department;
use common\models\DepartmentPhoneProject;
use common\models\Project;
use common\models\UserProfile;
use sales\model\department\department\Params;
use sales\model\project\entity\CustomData;
use yii\helpers\Json;

/**
 * Class RecordManager
 *
 * @property int|null $userId
 * @property int|null $projectId
 * @property int|null $departmentId
 * @property string|null $phone
 * @property int|null $contactId
 */
class RecordManager
{
    private ?int $userId;
    private ?int $projectId;
    private ?int $departmentId;
    private ?string $phone;
    private ?int $contactId;

    public function __construct(
        ?int $userId,
        ?int $projectId,
        ?int $departmentId,
        ?string $phone,
        ?int $contactId
    ) {
        $this->userId = $userId;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->phone = $phone;
        $this->contactId = $contactId;
    }

    public function isDisabledRecord(): bool
    {
        return $this->isDisabledSystem()
            || $this->isDisabledUser()
            || $this->isDisabledProject()
            || $this->isDisabledDepartment()
            || $this->isDisabledDepartmentPhoneProject()
            || $this->isDisabledContact();
    }

    private function isDisabledSystem(): bool
    {
        return (bool)(\Yii::$app->params['settings']['call_recording_disabled'] ?? false);
    }

    private function isDisabledUser(): bool
    {
        if (!$this->userId) {
            return false;
        }
        $profile = UserProfile::find()
            ->select(['up_call_recording_disabled'])
            ->andWhere(['up_user_id' => $this->userId])
            ->one();
        if (!$profile) {
            return false;
        }
        return (bool)$profile['up_call_recording_disabled'];
    }

    private function isDisabledProject(): bool
    {
        if (!$this->projectId) {
            return false;
        }
        $project = Project::find()->select(['id', 'custom_data'])->andWhere(['id' => $this->projectId])->asArray()->one();
        if (!$project) {
            return false;
        }
        if (!$project['custom_data']) {
            return false;
        }
        $customData = new CustomData($project['custom_data'], $project['id']);
        return $customData->isCallRecordingDisabled();
    }

    private function isDisabledDepartment(): bool
    {
        if (!$this->departmentId) {
            return false;
        }
        $department = Department::find()->select(['dep_params'])->andWhere(['dep_id' => $this->departmentId])->asArray()->one();
        if (!$department) {
            return false;
        }
        if (!$department['dep_params']) {
            return false;
        }
        try {
            $data = Json::decode($department['dep_params']);
            $params = new Params($data);
            return $params->isCallRecordingDisabled();
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Department params error',
                'error' => $e->getMessage(),
                'departmentId' => $this->departmentId,
            ], 'Department:getParams');
        }
        return false;
    }

    private function isDisabledDepartmentPhoneProject(): bool
    {
        if (!$this->phone) {
            return false;
        }

        $departmentPhoneProject = DepartmentPhoneProject::find()
            ->select(['dpp_params'])
            ->byPhone($this->phone, false)
            ->one();

        if (!$departmentPhoneProject) {
            return false;
        }

        if (!$departmentPhoneProject->dpp_params) {
            return false;
        }

        $dppParams = @json_decode($departmentPhoneProject->dpp_params, true);
        return (bool)($dppParams['call_recording_disabled'] ?? false);
    }

    private function isDisabledContact(): bool
    {
        if (!$this->contactId) {
            return false;
        }
        $contact = Client::find()->select(['cl_call_recording_disabled'])->andWhere(['id' => $this->contactId])->asArray()->one();
        if (!$contact) {
            return false;
        }
        return (bool)$contact['cl_call_recording_disabled'];
    }
}

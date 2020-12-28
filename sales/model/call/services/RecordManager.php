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
 * @property int|null $departmentPhoneProjectId
 * @property int|null $contactId
 */
class RecordManager
{
    private ?int $userId;
    private ?int $projectId;
    private ?int $departmentId;
    private ?int $contactId;
    private ?int $departmentPhoneProjectId;

    public function __construct(
        ?int $userId,
        ?int $projectId,
        ?int $departmentId,
        ?int $departmentPhoneProjectId,
        ?int $contactId
    ) {
        $this->userId = $userId;
        $this->projectId = $projectId;
        $this->departmentId = $departmentId;
        $this->contactId = $contactId;
        $this->departmentPhoneProjectId = $departmentPhoneProjectId;
    }

    public function canRecord(): bool
    {
        return $this->canSystem()
            && $this->canUser()
            && $this->canProject()
            && $this->canDepartment()
            && $this->canDepartmentPhoneProject()
            && $this->canContact();
    }

    private function canSystem(): bool
    {
        return !(bool)(\Yii::$app->params['settings']['call_recording_disabled'] ?? false);
    }

    private function canUser(): bool
    {
        if (!$this->userId) {
            return true;
        }
        $profile = UserProfile::find()
            ->select(['up_call_recording_disabled'])
            ->andWhere(['up_user_id' => $this->userId])
            ->one();
        if (!$profile) {
            return false;
        }
        return !(bool)$profile['up_call_recording_disabled'];
    }

    private function canProject(): bool
    {
        if (!$this->projectId) {
            return true;
        }
        $project = Project::find()->select(['id', 'custom_data'])->andWhere(['id' => $this->projectId])->asArray()->one();
        if (!$project) {
            return false;
        }
        if (!$project['custom_data']) {
            return true;
        }
        $customData = new CustomData($project['custom_data'], $project['id']);
        return $customData->isCallRecordingDisabled();
    }

    private function canDepartment(): bool
    {
        if (!$this->departmentId) {
            return true;
        }
        $department = Department::find()->select(['dep_params'])->andWhere(['dep_id' => $this->departmentId])->asArray()->one();
        if (!$department) {
            return false;
        }
        if (!$department['dep_params']) {
            return true;
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
        return true;
    }

    private function canDepartmentPhoneProject(): bool
    {
        if (!$this->departmentPhoneProjectId) {
            return true;
        }
        $departmentPhoneProject = DepartmentPhoneProject::find()
            ->select(['dpp_params'])
            ->andWhere(['dpp_id' => $this->departmentPhoneProjectId])
            ->asArray()
            ->one();

        if (!$departmentPhoneProject) {
            return false;
        }

        if (!$departmentPhoneProject['dpp_params']) {
            return true;
        }
        $dppParams = @json_decode($departmentPhoneProject['dpp_params'], true);
        return !(bool)($dppParams['call_recording_disabled'] ?? false);
    }

    private function canContact(): bool
    {
        if (!$this->contactId) {
            return true;
        }
        $contact = Client::find()->select(['cl_call_recording_disabled'])->andWhere(['id' => $this->contactId])->asArray()->one();
        if (!$contact) {
            return false;
        }
        return !(bool)$contact['cl_call_recording_disabled'];
    }
}

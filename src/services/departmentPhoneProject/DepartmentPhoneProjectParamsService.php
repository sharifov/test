<?php

namespace src\services\departmentPhoneProject;

use common\models\DepartmentPhoneProject;
use frontend\helpers\JsonHelper;
use modules\experiment\models\ExperimentTarget;
use src\helpers\DateHelper;
use yii\helpers\ArrayHelper;

/**
 * Class DepartmentPhoneProjectParamsService
 * @property DepartmentPhoneProject $departmentPhoneProject
 */
class DepartmentPhoneProjectParamsService
{
    private DepartmentPhoneProject $departmentPhoneProject;

    /**
     * @param DepartmentPhoneProject $departmentPhoneProject
     */
    public function __construct(DepartmentPhoneProject $departmentPhoneProject)
    {
        $this->departmentPhoneProject = $departmentPhoneProject;
    }

    public function getPhoneExperiments(): array
    {
        if (empty($this->departmentPhoneProject->dpp_params)) {
            return [];
        }
        $experiments = ArrayHelper::getValue(JsonHelper::decode($this->departmentPhoneProject->dpp_params), 'experiments', []);
        $experimentCodes = [];
        foreach ($experiments as $experiment) {
            if (!empty($experiment['enabled']) && isset($experiment['ex_code']) && $experiment['ex_code'] != '') {
                $experimentCodes[] = $experiment['ex_code'];
            }
        }

        return $experimentCodes;
    }

    /**
     * @param int $targetTypeId
     * @param int|null $phoneListId
     * @return void
     */
    public function processExperiments(int $targetTypeId, ?int $phoneListId): void
    {
        if ($phoneListId) {
            ExperimentTarget::processExperimentsCodes($targetTypeId, $phoneListId, $this->getPhoneExperiments());
        }
    }

    public function getCallFilterGuard(): array
    {
        if (empty($this->departmentPhoneProject->dpp_params)) {
            return [];
        }
        return ArrayHelper::getValue(JsonHelper::decode($this->departmentPhoneProject->dpp_params), 'callFilterGuard', []);
    }

    public function getCallFilterGuardEnable(): bool
    {
        return (bool) ArrayHelper::getValue($this->getCallFilterGuard(), 'enable', false);
    }

    public function getCallFilterGuardPercent(): int
    {
        return (int) ArrayHelper::getValue($this->getCallFilterGuard(), 'trustPercent', 100);
    }

    public function getCallFilterGuardBlockListExpiredMinutes(): int
    {
        return (int) ArrayHelper::getValue($this->getCallFilterGuard(), 'blockList.expiredMinutes', 60);
    }

    public function getCallFilterGuardBlockListEnabled(): bool
    {
        return (bool) ArrayHelper::getValue($this->getCallFilterGuard(), 'blockList.enabled', false);
    }

    public function getCallFilterGuardTrustCheckService(): array
    {
        return (array) ArrayHelper::getValue($this->getCallFilterGuard(), 'checkService', []);
    }

    public function getCallFilterGuardCallTerminate(): bool
    {
        return (bool) ArrayHelper::getValue($this->getCallFilterGuard(), 'callTerminate', false);
    }

    public function getCallFilterGuardEnabledFromDt(): ?string
    {
        if (
            ($enabledFromDt = ArrayHelper::getValue($this->getCallFilterGuard(), 'enabledFromDt')) &&
            (DateHelper::checkDateTime($enabledFromDt, 'Y-m-d H:i') || DateHelper::checkDateTime($enabledFromDt, 'Y-m-d'))
        ) {
            return $enabledFromDt;
        }
        return null;
    }

    public function getCallFilterGuardEnabledToDt(): ?string
    {
        if (
            ($enabledToDt = ArrayHelper::getValue($this->getCallFilterGuard(), 'enabledToDt')) &&
            (DateHelper::checkDateTime($enabledToDt, 'Y-m-d H:i') || DateHelper::checkDateTime($enabledToDt, 'Y-m-d'))
        ) {
            return $enabledToDt;
        }
        return null;
    }

    /**
     * @param int $projectId
     * @param array $users
     * @return array|DepartmentPhoneProject[]
     */
    public static function getDepartmentsWithCountOnlineUserByProjectId(int $projectId, array $users): array
    {
        $departments = [];
        $departmentsCount = [];
        /** @var DepartmentPhoneProject[] $departments */
        $departmentPhoneProjects = DepartmentPhoneProject::find()
            ->where(['dpp_project_id' => $projectId, 'dpp_enable' => true, 'dpp_allow_transfer' => true])
            ->andWhere(['>', 'dpp_dep_id', 0])
            ->withPhoneList()
            ->orderBy(['dpp_dep_id' => SORT_ASC])
            ->all();

        if (count($users) > 0) {
            foreach ($users as $model) {
                foreach ($model['departments'] as $dpId) {
                    if (array_key_exists($dpId, $departmentsCount)) {
                        $departmentsCount[$dpId]++;
                    } else {
                        $departmentsCount[$dpId] = 1;
                    }
                }
            }
        }

        foreach ($departmentPhoneProjects as $departmentPhoneProject) {
            $departments[$departmentPhoneProject->dpp_dep_id]['data'] = $departmentPhoneProject;
            $departments[$departmentPhoneProject->dpp_dep_id]['countAgents'] = array_key_exists($departmentPhoneProject->dpp_dep_id, $departmentsCount) ? $departmentsCount[$departmentPhoneProject->dpp_dep_id] : 0;
        }
        return $departments;
    }
}

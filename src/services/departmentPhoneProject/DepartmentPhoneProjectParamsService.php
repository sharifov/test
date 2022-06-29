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

    public function getPhoneExperimentList(): array
    {
        if (empty($this->departmentPhoneProject->dpp_params)) {
            return [];
        }
        return array_unique(array_column(ArrayHelper::getValue(JsonHelper::decode($this->departmentPhoneProject->dpp_params), 'experiments', []), 'ex_code'));
    }

    public function saveExperimentList( int $targetTypeId, int $callId): array
    {
        ExperimentTarget::saveExperimentList($targetTypeId, $callId, $this->getPhoneExperimentList());
    }

    public function updateExperimentList( int $oldTargetTypeId, int $newTargetTypeId, int $callId): array
    {
        ExperimentTarget::updateAll(['ext_target_type_id' => $newTargetTypeId], ['ext_target_type_id' => $oldTargetTypeId, 'ext_target_id' => $callId]);
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
}

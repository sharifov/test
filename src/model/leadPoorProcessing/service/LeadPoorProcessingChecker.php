<?php

namespace src\model\leadPoorProcessing\service;

use common\models\Employee;
use common\models\Lead;
use modules\featureFlag\FFlag;
use src\model\leadData\services\LeadDataCreateService;
use src\model\leadPoorProcessingData\abac\dto\LeadPoorProcessingAbacDto;
use src\model\leadPoorProcessingData\abac\LeadPoorProcessingAbacObject;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingData;
use src\model\leadPoorProcessingData\entity\LeadPoorProcessingDataQuery;
use Yii;

/**
 * Class LeadPoorProcessingChecker
 */
class LeadPoorProcessingChecker
{
    private Lead $lead;
    private string $dataKey;
    private LeadPoorProcessingData $rule;

    public function __construct(Lead $lead, string $dataKey)
    {
        $this->lead = $lead;
        $this->dataKey = $dataKey;
        if (!$rule = LeadPoorProcessingDataQuery::getRuleByKey($dataKey)) {
            throw new \RuntimeException('Rule not found by key(' . $dataKey . ')');
        }
        $this->rule = $rule;
    }

    public function check(): void
    {
        /** @fflag FFlag::FF_LPP_ENABLE, Lead Poor Processing Enable/Disable */
        if (!Yii::$app->ff->isEnable(FFlag::FF_KEY_LPP_ENABLE)) {
            throw new \RuntimeException('Feature Flag (' . FFlag::FF_KEY_LPP_ENABLE . ') not enabled');
        }
        if (!$this->rule->isEnabled()) {
            throw new \RuntimeException('Rule (' . $this->dataKey . ') not enabled');
        }
        if (!$employee = Employee::find()->where(['id' => $this->lead->employee_id])->limit(1)->one()) {
            throw new \RuntimeException('LeadOwner not found by ID(' . $this->lead->employee_id . ')');
        }

        /** @abac LeadPoorProcessingAbacDto, LeadPoorProcessingAbacObject::OBJ_PERMISSION_RULE, LeadPoorProcessingAbacObject::DYNAMICAL_RULE, access to LPP logic */
        $leadAbacDto = new LeadPoorProcessingAbacDto($this->lead, (int) $this->lead->employee_id);
        if (!Yii::$app->abac->can($leadAbacDto, LeadPoorProcessingAbacObject::OBJ_PERMISSION_RULE, $this->dataKey, $employee)) {
            LeadDataCreateService::getOrCreateLppExclude($this->lead->id, $this->dataKey, $employee->username);

            throw new \RuntimeException('Abac access is failed. (' .
                LeadPoorProcessingAbacObject::OBJ_PERMISSION_RULE . '/' . $this->dataKey . '/' . $employee->username . ')');
        }
    }

    public function isChecked(): bool
    {
        try {
            $this->check();
            return true;
        } catch (\Throwable $throwable) {
            return false;
        }
    }

    public function getRule(): LeadPoorProcessingData
    {
        return $this->rule;
    }
}

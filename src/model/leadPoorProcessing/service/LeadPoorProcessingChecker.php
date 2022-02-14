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
        if (!Yii::$app->ff->can(FFlag::FF_KEY_LPP_ENABLE)) {
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
            LeadDataCreateService::createOrUpdateLppExclude($this->lead->id, (new \DateTimeImmutable()));

            throw new \RuntimeException('Abac access is failed. (' .
                LeadPoorProcessingAbacObject::OBJ_PERMISSION_RULE . '/' . $this->dataKey . ')');
        }
    }

    public function isChecked(): bool
    {
        if (!Yii::$app->ff->can(FFlag::FF_KEY_LPP_ENABLE)) {
            return false;
        }

        if (!$this->rule->isEnabled()) {
            return false;
        }

        if (!$employee = Employee::find()->where(['id' => $this->lead->employee_id])->limit(1)->one()) {
            return false;
        }

        /** @abac LeadPoorProcessingAbacDto, LeadPoorProcessingAbacObject::OBJ_PERMISSION_RULE, LeadPoorProcessingAbacObject::DYNAMICAL_RULE, access to LPP logic */
        $leadAbacDto = new LeadPoorProcessingAbacDto($this->lead, (int) $this->lead->employee_id);
        if (!Yii::$app->abac->can($leadAbacDto, LeadPoorProcessingAbacObject::OBJ_PERMISSION_RULE, $this->dataKey, $employee)) {
            LeadDataCreateService::createOrUpdateLppExclude($this->lead->id, (new \DateTimeImmutable()));
            return false;
        }
        return true;
    }
}

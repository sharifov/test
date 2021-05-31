<?php

namespace sales\model\leadData\services;

use sales\helpers\ErrorsToStringHelper;
use sales\model\leadData\entity\LeadData;
use sales\model\leadData\repository\LeadDataRepository;
use yii\helpers\ArrayHelper;

/**
 * Class LeadDataCreateService
 * @property array $inserted
 * @property array $errors
 */
class LeadDataCreateService
{
    private array $inserted = [];
    private array $errors = [];

    public function createFromApi(array $leadDatas, int $leadId): void
    {
        $leadDataRepository = new LeadDataRepository();
        foreach ($leadDatas as $value) {
            $leadData = LeadData::create(
                $leadId,
                ArrayHelper::getValue($value, 'field_key'),
                ArrayHelper::getValue($value, 'field_value')
            );

            if ($leadData->validate()) {
                $leadDataRepository->save($leadData);
                $this->inserted[] = $leadData->serialize();
            } else {
                $warning = [
                    'message' => ErrorsToStringHelper::extractFromModel($leadData),
                    'data' => $value,
                ];
                $this->errors[] = $warning;
                \Yii::warning($warning, 'LeadController:actionCreate:LeadDataNotValid');
            }
        }
    }

    public function getInserted(): array
    {
        return $this->inserted;
    }

    public function getErrors(): array
    {
        return $this->errors;
    }
}

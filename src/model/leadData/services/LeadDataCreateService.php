<?php

namespace src\model\leadData\services;

use common\models\Lead;
use DateTime;
use src\helpers\ErrorsToStringHelper;
use src\model\leadData\entity\LeadData;
use src\model\leadData\repository\LeadDataRepository;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
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

    public static function createValueTimestamp(int $leadId, string $key): LeadData
    {
        $leadDataRepository = new LeadDataRepository();
        $leadData = LeadData::create(
            $leadId,
            $key,
            (string) (new DateTime())->getTimestamp()
        );
        if (!$leadData->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadData));
        }

        $leadDataRepository->save($leadData);
        return $leadData;
    }

    public function createWeEmailReplied(Lead $lead): LeadData
    {
        return self::createValueTimestamp($lead->id, LeadDataKeyDictionary::KEY_WE_EMAIL_REPLIED);
    }

    public function createWeFirstCallNotPicked(Lead $lead): LeadData
    {
        return self::createValueTimestamp($lead->id, LeadDataKeyDictionary::KEY_WE_FIRST_CALL_NOT_PICKED);
    }

    public static function createValueDT(int $leadId, string $key, \DateTimeImmutable $date): LeadData
    {
        $leadDataRepository = new LeadDataRepository();
        $leadData = LeadData::create(
            $leadId,
            $key,
            $date->format('Y-m-d H:i:s')
        );
        if (!$leadData->validate()) {
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadData));
        }

        $leadDataRepository->save($leadData);
        return $leadData;
    }

    public static function createOrUpdateLppExclude(int $leadId, \DateTimeImmutable $date): LeadData
    {
        if ($leadData = self::getOne($leadId, LeadDataKeyDictionary::KEY_LPP_EXCLUDE)) {
            $leadData->ld_field_value = $date->format('Y-m-d H:i:s');
            (new LeadDataRepository())->save($leadData);
            return $leadData;
        }
        return self::createValueDT($leadId, LeadDataKeyDictionary::KEY_LPP_EXCLUDE, $date);
    }

    public static function isExist(int $leadId, string $key): bool
    {
        return LeadData::find()
            ->where(['ld_lead_id' => $leadId])
            ->andWhere(['ld_field_key' => $key])
            ->exists();
    }

    public static function getOne(int $leadId, string $key): ?LeadData
    {
        return LeadData::find()
            ->where(['ld_lead_id' => $leadId])
            ->andWhere(['ld_field_key' => $key])
            ->one();
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

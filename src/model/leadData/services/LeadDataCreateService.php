<?php

namespace src\model\leadData\services;

use common\models\Lead;
use DateTime;
use src\helpers\app\AppHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\leadData\entity\LeadData;
use src\model\leadData\entity\LeadDataQuery;
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

    public static function getOrCreateLppExclude(int $leadId, string $keyData, string $username): ?LeadData
    {
        $value = 'keyData (' . $keyData . ') username(' . $username . ')';
        if ($leadData = LeadDataQuery::getOneByLeadKeyValue($leadId, LeadDataKeyDictionary::KEY_LPP_EXCLUDE, $value)) {
            return $leadData;
        }

        $logData = [
            'leadId' => $leadId,
            'keyData' => $keyData,
            'username' => $username,
        ];

        try {
            $leadDataRepository = new LeadDataRepository();
            $leadData = LeadData::create(
                $leadId,
                LeadDataKeyDictionary::KEY_LPP_EXCLUDE,
                $value
            );
            if (!$leadData->validate()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($leadData));
            }

            $leadDataRepository->save($leadData);
            return $leadData;
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::warning($message, 'LeadDataCreateService:getOrCreateLppExclude:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), $logData);
            \Yii::error($message, 'LeadDataCreateService:getOrCreateLppExclude:Throwable');
        }
        return null;
    }

    public static function createByCallId(int $leadId, int $callId): void
    {
        try {
            $leadDataRepository = \Yii::createObject(LeadDataRepository::class);
            $leadData = LeadData::create($leadId, LeadDataKeyDictionary::KEY_CREATED_BY_CALL_ID, $callId);
            $leadDataRepository->save($leadData);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'leadId' => $leadId,
                'callId' => $callId,
            ], 'LeadDataCreateService::createByCallId');
        }
    }

    public static function isExist(int $leadId, string $key): bool
    {
        return LeadData::find()
            ->where(['ld_lead_id' => $leadId])
            ->andWhere(['ld_field_key' => $key])
            ->exists();
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

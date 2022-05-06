<?php

namespace modules\objectSegment\src\service\lead;

use modules\objectSegment\src\contracts\ObjectSegmentAssigmentServiceInterface;
use src\model\leadData\entity\LeadData;
use src\model\leadDataKey\services\LeadDataKeyDictionary;
use src\repositories\lead\LeadRepository;

class LeadObjectSegmentAssignService implements ObjectSegmentAssigmentServiceInterface
{
    private LeadRepository $leadRepository;

    public function __construct(LeadRepository $leadRepository)
    {
        $this->leadRepository = $leadRepository;
    }

    public function assign(int $entityId, array $values): void
    {
        try {
            $transaction = \Yii::$app->db->beginTransaction();
            $lead = $this->leadRepository->find($entityId);
            if (empty($lead)) {
                throw new \DomainException('Lead not found');
            }
            LeadData::deleteAll(
                [
                    'AND',
                    ['ld_lead_id' => $lead->id],
                    ['ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT]
                ]
            );
            $inserts = [];
            foreach ($values as $value) {
                $inserts[] = [
                    'ld_lead_id' => $lead->id,
                    'ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT,
                    'ld_field_value' => $value
                ];
            }
            if (count($inserts)) {
                \Yii::$app
                    ->db
                    ->createCommand()
                    ->batchInsert(LeadData::tableName(), ['ld_lead_id', 'ld_field_key','ld_field_value'], $inserts)
                    ->execute();
            }
            $transaction->commit();
        } catch (\RuntimeException | \DomainException $e) {
            $transaction->rollBack();
            \Yii::warning(
                \src\helpers\app\AppHelper::throwableLog($e),
                'LeadObjectSegmentAssignService:assign:exception'
            );
        } catch (\Throwable $e) {
            $transaction->rollBack();
            \Yii::error(
                \src\helpers\app\AppHelper::throwableLog($e),
                'LeadObjectSegmentAssignService:assign:Throwable'
            );
        }
    }
}

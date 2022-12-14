<?php

namespace modules\objectSegment\src\service\lead;

use modules\featureFlag\FFlag;
use modules\objectSegment\src\contracts\ObjectSegmentAssigmentServiceInterface;
use modules\objectSegment\src\contracts\ObjectSegmentListContract;
use src\model\leadBusinessExtraQueue\service\LeadBusinessExtraQueueService;
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

    public function assign(int $entityId, array $objectSegmentListKeys): void
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
            $currentDateTime = date('Y-m-d H:i:s');
            $inserts = [];
            foreach ($objectSegmentListKeys as $value) {
                $inserts[] = [
                    'ld_lead_id' => $lead->id,
                    'ld_field_key' => LeadDataKeyDictionary::KEY_LEAD_OBJECT_SEGMENT,
                    'ld_field_value' => $value,
                    'ld_created_dt' => $currentDateTime
                ];
            }
            if (count($inserts)) {
                \Yii::$app
                    ->db
                    ->createCommand()
                    ->batchInsert(LeadData::tableName(), ['ld_lead_id', 'ld_field_key','ld_field_value', 'ld_created_dt'], $inserts)
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

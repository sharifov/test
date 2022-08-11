<?php

namespace src\model\voip\overridePhoneTo;

use src\model\callLog\entity\callLog\CallLog;
use src\model\callLog\entity\callLog\CallLogType;
use src\model\phoneList\entity\PhoneList;

/**
 * Class OverrideService
 *
 * @property array $report
 */
class OverrideService
{
    private array $report = [
        'from' => '',
        'to' => '',
        'found' => 0,
        'processing' => 0,
        'success' => 0,
        'error' => 0,
        'timeStart' => '',
        'timeEnd' => '',
        'executionTime' => 0,
    ];

    public function override(\DateTimeImmutable $fromDate, \DateTimeImmutable $toDate, array $phones): array
    {
        $this->report['from'] = $fromDate->format('Y-m-d H:i:s');
        $this->report['to'] = $toDate->format('Y-m-d H:i:s');
        $calls = $this->getCalls($fromDate, $toDate, $phones);
        $this->report['found'] = count($calls);
        $timeStart = new \DateTimeImmutable();
        $this->report['timeStart'] = $timeStart->format('Y-m-d H:i:s');
        foreach ($calls as $call) {
            $forwardedFrom = $this->getOriginalForwardedFromNumber($call['cl_call_sid']);
            if (!$forwardedFrom) {
                continue;
            }
            $phoneListId =  PhoneList::find()->select(['pl_id'])->byPhone($forwardedFrom)->scalar();
            if (!$phoneListId) {
                continue;
            }
            $this->report['processing']++;
            $this->updateCall($call['cl_id'], $call['cl_group_id'], $phoneListId, $forwardedFrom);
        }
        $timeEnd = new \DateTimeImmutable();
        $this->report['timeEnd'] = $timeEnd->format('Y-m-d H:i:s');
        $this->report['executionTime'] = $timeEnd->diff($timeStart)->format('%s') . ' sec';
        return $this->report;
    }

    private function updateCall(int $callId, int $groupId, int $phoneListId, string $forwardedNumber): void
    {
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            CallLog::updateAll(
                ['cl_phone_list_id' => $phoneListId, 'cl_phone_to' => $forwardedNumber],
                'cl_id = :cl_id',
                [':cl_id' => $callId]
            );
            CallLog::updateAll(
                ['cl_phone_list_id' => $phoneListId],
                'cl_group_id = :cl_group_id',
                [':cl_group_id' => $groupId]
            );
            $transaction->commit();
            $this->report['success']++;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            $this->report['error']++;
            \Yii::error([
                'message' => 'Cant update call',
                'error' => $e->getMessage(),
                'callLogId' => $callId,
            ], 'OverridePhoneToService:updateCall');
        }
    }

    private function getOriginalForwardedFromNumber(string $callSid): ?string
    {
        try {
            return \Yii::$app->comms->getOriginalForwardedFromNumber($callSid);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cant get forwarded from number',
                'error' => $e->getMessage(),
                'callSid' => $callSid,
            ], 'OverridePhoneToService:getOriginalForwardedFromNumber');
            return null;
        }
    }

    private function getCalls(\DateTimeImmutable $fromDate, \DateTimeImmutable $toDate, array $phones): array
    {
        return CallLog::find()
            ->select(['cl_id', 'cl_group_id', 'cl_call_sid'])
            ->andWhere(['>', 'cl_call_created_dt', $fromDate->format('Y-m-d H:i:s')])
            ->andWhere(['<=', 'cl_call_created_dt', $toDate->format('Y-m-d H:i:s')])
            ->andWhere(['cl_phone_to' => $phones])
            ->andWhere('cl_id = cl_group_id')
            ->andWhere(['cl_type_id' => CallLogType::IN])
            ->asArray()
            ->all();
    }
}

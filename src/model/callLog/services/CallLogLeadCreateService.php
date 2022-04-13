<?php

namespace src\model\callLog\services;

use src\model\callLog\entity\callLogLead\CallLogLead;

/**
 * Class CallLogLeadCreateService
 */
class CallLogLeadCreateService
{
    public static function create(int $callId, int $leadId): void
    {
        try {
            $callLogLead = new CallLogLead([
                'cll_cl_id' => $callId,
                'cll_lead_id' => $leadId,
            ]);
            $callLogLead->save(false);
        } catch (\Throwable $e) {
            if (strpos($e->getMessage(), 'Duplicate entry') === false) {
                \Yii::error([
                    'message' => $e->getMessage(),
                    'callId' => $callId,
                    'leadId' => $leadId,
                ], 'CallLogLeadCreateService::create');
            }
        }
    }
}

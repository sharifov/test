<?php

namespace sales\model\leadRedial\queue;

use common\models\DepartmentPhoneProject;
use common\models\Lead;
use sales\helpers\app\AppHelper;
use sales\services\lead\qcall\Config;
use sales\services\lead\qcall\FindPhoneParams;
use sales\services\lead\qcall\QCallService;

/**
 * Class AgentPhone
 *
 * @property QCallService $qCallService
 */
class AgentPhone
{
    private QCallService $qCallService;

    public function __construct(QCallService $qCallService)
    {
        $this->qCallService = $qCallService;
    }

    public function findOrUpdatePhone(Lead $lead): ?string
    {
        if (
            ($qCall = $lead->leadQcall)
            && ($phoneFrom = $qCall->lqc_call_from)
            && DepartmentPhoneProject::find()->enabled()->redial()->byPhone($phoneFrom, false)->exists()
        ) {
            return $phoneFrom;
        }

        if ($qCall) {
            try {
                $phoneFrom = $this->qCallService->updateCallFrom(
                    $qCall,
                    new Config($lead->status, $lead->getCountOutCallsLastFlow()),
                    new FindPhoneParams($lead->project_id, $lead->l_dep_id)
                );
                if ($phoneFrom) {
                    return $phoneFrom;
                }
            } catch (\Throwable $e) {
                \Yii::error([
                    'message' => 'Update qcall lead phone from error',
                    'leadId' => $lead->id,
                    'error' => $e->getMessage(),
                    'exception' => AppHelper::throwableLog($e),
                ], 'leadRedial:services:AgentPhone:findOrUpdatePhone');
            }
        }

//        if (($phone = Project::findOne($lead->project_id)) && $phone->contactInfo->phone) {
//            return $phone->contactInfo->phone;
//        }

        return null;
    }
}

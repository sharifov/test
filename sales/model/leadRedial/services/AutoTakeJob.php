<?php

namespace sales\model\leadRedial\services;

use common\models\Call;
use common\models\Notifications;
use sales\helpers\app\AppHelper;
use sales\model\leadUserConversion\entity\LeadUserConversion;
use sales\model\leadUserConversion\repository\LeadUserConversionRepository;
use sales\model\leadUserConversion\service\LeadUserConversionDictionary;
use sales\repositories\lead\LeadRepository;
use sales\services\lead\qcall\QCallService;
use sales\services\TransactionManager;
use yii\helpers\Url;
use yii\queue\JobInterface;

/**
 * Class AutoTakeJob
 *
 * @property int $callId
 */
class AutoTakeJob implements JobInterface
{
    public $callId;

    public function __construct(int $callId)
    {
        $this->callId = $callId;
    }

    public function execute($queue)
    {
        $call = Call::find()->byId($this->callId)->one();
        if (!$call) {
            \Yii::error(['message' => 'Not found call. Call ID: ' . $this->callId, 'AutoTakeJob']);
            return;
        }

        if (!$call->isStatusInProgress()) {
            return;
        }

        if (!$call->c_lead_id) {
            \Yii::error(['message' => 'Not found lead. Call ID: ' . $this->callId, 'AutoTakeJob']);
            return;
        }
        $lead = $call->cLead;

        $userId = $call->c_created_user_id;
        if (!$userId) {
            \Yii::error(['message' => 'Not found created user. Call ID: ' . $this->callId, 'AutoTakeJob']);
            return;
        }

        $leadChanged = false;
        if (!$lead->isOwner($userId) || !$lead->isProcessing()) {
            $leadChanged = true;
            $lead->answered();
            $lead->processing($userId, $userId, 'Lead redial');
        }

        $leadUserConversion = LeadUserConversion::create(
            $lead->id,
            $userId,
            LeadUserConversionDictionary::DESCRIPTION_TAKE
        );

        try {
            $transactionManager = \Yii::createObject(TransactionManager::class);
            $qCallService = \Yii::createObject(QCallService::class);
            $leadRepository = \Yii::createObject(LeadRepository::class);

            $transactionManager->wrap(function () use ($lead, $leadRepository, $leadUserConversion, $qCallService, $leadChanged) {
                $qCallService->remove($lead->id);
                if ($leadChanged) {
                    $leadRepository->save($lead);
                }
                (new LeadUserConversionRepository())->save($leadUserConversion);
            });
            Notifications::pub(
                ['user-' . $userId],
                'leadRedialAutoTake',
                [
                    'data' => [
                        'callSid' => $call->c_call_sid,
                        'leadId' => $lead->id,
                        'leadGid' => $lead->gid,
                    ],
                ],
            );
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'exception' => AppHelper::throwableLog($e),
            ], 'AutoTakeJob');
        }
    }
}

<?php

namespace sales\model\leadRedial\queue;

use common\components\purifier\Purifier;
use common\models\Call;
use common\models\Lead;
use common\models\Notifications;
use frontend\widgets\notification\NotificationMessage;
use sales\helpers\app\AppHelper;
use sales\model\leadUserConversion\service\LeadUserConversionDictionary;
use sales\model\leadUserConversion\service\LeadUserConversionService;
use sales\repositories\lead\LeadRepository;
use sales\services\lead\qcall\QCallService;
use sales\services\TransactionManager;
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

        try {
            $transactionManager = \Yii::createObject(TransactionManager::class);
            $qCallService = \Yii::createObject(QCallService::class);
            $leadRepository = \Yii::createObject(LeadRepository::class);
            $leadUserConversionService = \Yii::createObject(LeadUserConversionService::class);


            $transactionManager->wrap(function () use ($lead, $leadRepository, $qCallService, $leadChanged, $userId, $leadUserConversionService) {
                $qCallService->remove($lead->id);
                if ($leadChanged) {
                    $leadRepository->save($lead);
                }
                $leadUserConversionService->add(
                    $lead->id,
                    $userId,
                    LeadUserConversionDictionary::DESCRIPTION_TAKE,
                    $userId
                );
            });
            $this->sendAutoTakeCommand($userId, $call, $lead);
            $this->sendAssignLeadNotification($lead);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => $e->getMessage(),
                'exception' => AppHelper::throwableLog($e),
            ], 'AutoTakeJob');
        }
    }

    private function sendAssignLeadNotification(Lead $lead): void
    {
        $body = \Yii::t(
            'email',
            "New lead (Id: {lead_id}) automatically assigned.",
            [
                'lead_id' => Purifier::createLeadShortLink($lead),
            ]
        );

        if ($ntf = Notifications::create($lead->employee_id, 'Lead assign', $body, Notifications::TYPE_INFO, true)) {
            $dataNotification = (\Yii::$app->params['settings']['notification_web_socket']) ? NotificationMessage::add($ntf) : [];
            Notifications::publish('getNewNotification', ['user_id' => $lead->employee_id], $dataNotification);
        } else {
            \Yii::warning(
                'Not created notification to employee_id: ' . $lead->employee_id . ', lead: ' . $lead->id,
                'AutoTakeJob:sendAssignLeadNotification'
            );
        }
    }

    public function sendAutoTakeCommand(int $userId, Call $call, Lead $lead): void
    {
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
    }
}

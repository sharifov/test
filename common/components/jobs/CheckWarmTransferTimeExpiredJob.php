<?php

namespace common\components\jobs;

use common\components\purifier\Purifier;
use common\models\Call;
use common\models\CallUserAccess;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use yii\queue\JobInterface;

/**
 * Class CheckWarmTransferTimeExpiredJob
 *
 * @property $callId
 * @property $toUserId
 * @property $conferenceSid
 * @property $keeperSid
 * @property $recordingDisabled
 */
class CheckWarmTransferTimeExpiredJob implements JobInterface
{
    public $callId;
    public $toUserId;
    public $conferenceSid;
    public $keeperSid;
    public $recordingDisabled;

    public function __construct(int $callId, int $toUserId, string $conferenceSid, string $keeperSid, bool $recordingDisabled)
    {
        $this->callId = $callId;
        $this->toUserId = $toUserId;
        $this->conferenceSid = $conferenceSid;
        $this->keeperSid = $keeperSid;
        $this->recordingDisabled = $recordingDisabled;
    }

    public function execute($queue)
    {
        $access = CallUserAccess::find()->byWarmTransfer()->byCall($this->callId)->byUser($this->toUserId)->one();
        if (!$access) {
            return;
        }
        $access->noAnsweredCall();
        $access->save();

        $this->sendNotificationsToTransferedUser($access->cuaCall);
        $this->sendNotificationToCurrentOwner($access);

        try {
            $agentParticipant = ConferenceParticipant::find()->andWhere([
                'cp_cf_sid' => $this->conferenceSid,
                'cp_call_sid' => $this->keeperSid,
                'cp_status_id' => ConferenceParticipant::STATUS_HOLD
            ])->exists();

            if (!$agentParticipant) {
                return;
            }
            $result = \Yii::$app->communication->unholdConferenceCall(
                $this->conferenceSid,
                $this->keeperSid,
                $this->recordingDisabled
            );
            if ($result['error']) {
                \Yii::error([
                    'message' => 'CheckWarmTransferTimeExpired error',
                    'error' => $result['message'],
                    'callId' => $this->callId,
                    'toUserId' => $this->toUserId,
                    'conferenceSid' => $this->conferenceSid,
                    'keeperSid' => $this->keeperSid,
                ], 'CheckWarmTransferTimeExpiredJob');
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'CheckWarmTransferTimeExpired error',
                'error' => $e->getMessage(),
                'callId' => $this->callId,
                'toUserId' => $this->toUserId,
                'conferenceSid' => $this->conferenceSid,
                'keeperSid' => $this->keeperSid,
            ], 'CheckWarmTransferTimeExpiredJob');
        }
    }

    private function isAutoUnhold(): bool
    {
        //todo
    }

    private function sendNotificationsToTransferedUser(Call $call): void
    {
        $message = 'Missed Call. Id: ' . $call->c_id;
        if ($call->c_lead_id && $call->cLead) {
            $message .= ', Lead (Id: ' . Purifier::createLeadShortLink($call->cLead) . ')';
        }
        if ($call->c_case_id && $call->cCase) {
            $message .= ', Case (Id: ' . Purifier::createCaseShortLink($call->cCase) . ')';
        }
        Notifications::createAndPublish(
            $this->toUserId,
            'Missed Call',
            $message,
            Notifications::TYPE_WARNING,
            true
        );
    }

    private function sendNotificationToCurrentOwner(CallUserAccess $access): void
    {
        Notifications::createAndPublish(
            $access->cuaCall->c_created_user_id,
            'Warm transfer',
            'Warm transfer timeout expired. Call Id: ' . $access->cua_call_id,
            Notifications::TYPE_WARNING,
            true
        );
    }
}

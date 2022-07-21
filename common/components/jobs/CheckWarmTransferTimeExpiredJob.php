<?php

namespace common\components\jobs;

use common\components\purifier\Purifier;
use common\models\Call;
use common\models\CallUserAccess;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use src\model\call\helper\CallHelper;
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
class CheckWarmTransferTimeExpiredJob extends BaseJob implements JobInterface
{
    public $callId;
    public $toUserId;
    public $conferenceSid;
    public $keeperSid;
    public $recordingDisabled;

    public function __construct(
        int $callId,
        int $toUserId,
        string $conferenceSid,
        string $keeperSid,
        bool $recordingDisabled
    ) {
        $this->callId = $callId;
        $this->toUserId = $toUserId;
        $this->conferenceSid = $conferenceSid;
        $this->keeperSid = $keeperSid;
        $this->recordingDisabled = $recordingDisabled;
        parent::__construct();
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        $access = CallUserAccess::find()->byWarmTransfer()->byCall($this->callId)->byUser($this->toUserId)->one();
        if (!$access) {
            $access = CallUserAccess::find()->accepted()->byCall($this->callId)->byUser($this->toUserId)->one();
            if (!$access) {
                return;
            }
            $oldConferenceIsActive = Conference::find()->bySid($this->conferenceSid)->active()->exists();
            if (!$oldConferenceIsActive) {
                return;
            }
            $ringingAcceptedCallByNewUser = Call::find()->select(['c_call_sid'])->byCreatedUser($this->toUserId)->byParentId($this->callId)->ringing()->asArray()->scalar();
            if (!$ringingAcceptedCallByNewUser) {
                return;
            }
            $this->cancelCall($ringingAcceptedCallByNewUser);
        }
        $access->noAnsweredCall();
        $access->save();

        $this->sendNotificationsToTransferedUser($access->cuaCall);
        $this->sendNotificationToCurrentOwner($access);

        if (!CallHelper::warmTransferAutoUnholdEnabled($access->cuaCall->c_dep_id)) {
            return;
        }

        try {
            $agentParticipant = ConferenceParticipant::find()->andWhere([
                'cp_cf_sid' => $this->conferenceSid,
                'cp_call_sid' => $this->keeperSid,
                'cp_status_id' => ConferenceParticipant::STATUS_HOLD
            ])->exists();

            if (!$agentParticipant) {
                return;
            }
            $result = \Yii::$app->comms->unholdConferenceCall(
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

    private function cancelCall(string $sid): void
    {
        try {
            $result = \Yii::$app->comms->hangUp($sid);
            $isError = (bool)($result['error'] ?? true);
            if ($isError) {
                \Yii::error([
                    'message' => 'Cancel warm transfer accepted call',
                    'error' => $result['message'],
                    'sid' => $sid
                ], 'CheckWarmTransferTimeExpiredJob');
            }
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Cancel warm transfer accepted call',
                'error' => $e->getMessage(),
                'sid' => $sid
            ], 'CheckWarmTransferTimeExpiredJob');
        }
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

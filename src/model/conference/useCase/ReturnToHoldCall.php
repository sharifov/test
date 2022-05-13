<?php

namespace src\model\conference\useCase;

use common\components\CommunicationService;
use common\models\Call;
use common\models\CallUserAccess;
use common\models\Conference;
use common\models\Notifications;
use frontend\widgets\newWebPhone\call\socket\RemoveIncomingRequestMessage;
use yii\helpers\VarDumper;

/**
 * Class ReturnToHoldCall
 *
 * @property CommunicationService $communication
 * @property array $messages
 */
class ReturnToHoldCall
{
    private CommunicationService $communication;

    public function __construct()
    {
        $this->communication = \Yii::$app->communication;
    }

    public function return(Call $call, int $userId, string $deviceIdentity): bool
    {
        if (!$call->isOwner($userId)) {
            $this->log([
                'error' => 'User is not Owner of Call',
                'user_id' => $userId,
                'call_id' => $call->c_id,
            ]);
            return false;
        }

        if (!$call->isHold()) {
            $this->log([
                'error' => 'Call is Not Hold',
                'user_id' => $userId,
                'call_id' => $call->c_id,
            ]);
            return false;
        }

        if ($call->isOut() && $call->cParent && $call->cParent->c_call_sid) {
            $parentCallSid = $call->c_parent_call_sid;
        } elseif ($call->isIn()) {
            $parentCallSid = $call->c_call_sid;
        } else {
            $this->log([
                'error' => 'Not found Parent Call SID',
                'user_id' => $userId,
                'call_id' => $call->c_id,
            ]);
            return false;
        }

        $conference = Conference::findOne([
            'cf_id' => $call->c_conference_id,
            'cf_status_id' => Conference::STATUS_START,
            'cf_created_user_id' => $userId
        ]);

        if (!$conference) {
            $this->log([
                'error' => 'Not found Start Conference',
                'user_id' => $userId,
                'call_id' => $call->c_id,
                'conference_id' => $call->c_conference_id,
            ]);
            return false;
        }

        try {
            $result = $this->communication->returnToConferenceCall(
                $call->c_call_sid,
                $parentCallSid,
                $conference->cf_friendly_name,
                $conference->cf_sid,
                $deviceIdentity,
                $userId,
                $conference->isRecordingDisabled(),
                $call->getDataPhoneListId(),
                $call->c_to,
                $call->c_from,
                $call->c_project_id ? $call->cProject->name : '',
                $call->getSourceName(),
                Call::TYPE_LIST[Call::CALL_TYPE_RETURN],
            );
            $isError = (bool)($result['error'] ?? true);
            if ($isError) {
                $this->log([
                    'error' => $result['message'],
                    'user_id' => $userId,
                    'call_id' => $call->c_id,
                    'conference_d' => $call->c_conference_id,
                ]);
                return false;
            }
        } catch (\Throwable $e) {
            $this->log([
                'error' => $e->getMessage(),
                'user_id' => $userId,
                'call_id' => $call->c_id,
                'conference_d' => $call->c_conference_id,
            ]);
            return false;
        }

        return true;
    }

    public function acceptHoldCall(CallUserAccess $callUserAccess): bool
    {
        $callUserAccess->acceptCall();
        if (!$callUserAccess->save()) {
            return false;
        }
        if ($call = $callUserAccess->cuaCall) {
            $call->setStatusDelay();
            if ($call->save()) {
                Notifications::publish(RemoveIncomingRequestMessage::COMMAND, ['user_id' => $callUserAccess->cua_user_id], RemoveIncomingRequestMessage::create($call->c_call_sid));
                Notifications::pingUserMap();
                return true;
            }
            \Yii::error(VarDumper::dumpAsString($call->getErrors()), 'ReturnToHoldCall:acceptHoldCall');
        }
        return false;
    }

    private function log($message): void
    {
        \Yii::error(VarDumper::dumpAsString($message), static::class);
    }
}

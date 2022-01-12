<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\ConferenceParticipant;
use src\model\conference\service\ConferenceDataService;
use src\model\conference\socket\SocketCommands;
use src\model\conference\useCase\statusCallBackEvent\ConferenceStatusCallbackHandler;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class UpdateConferenceParticipantCallIdJob
 *
 * @property string $callSid
 * @property string $conferenceSid
 * @property int $conferenceId
 */
class UpdateConferenceParticipantCallIdJob extends BaseJob implements JobInterface
{
    public string $callSid;
    public string $conferenceSid;
    public int $conferenceId;

    public function __construct(string $callSid, string $conferenceSid, int $conferenceId)
    {
        $this->callSid = $callSid;
        $this->conferenceSid = $conferenceSid;
        $this->conferenceId = $conferenceId;
        parent::__construct();
    }

    public function execute($queue)
    {
        $this->waitingTimeRegister();
        try {
            $call = $this->getCall($this->callSid);
            if ($call->c_conference_id !== $this->conferenceId) {
                $call->c_conference_sid = $this->conferenceSid;
                $call->c_conference_id = $this->conferenceId;
                if (!$call->save()) {
                    \Yii::error(VarDumper::dumpAsString([
                        'errors' => $call->getErrors(),
                        'model' => $call->getAttributes(),
                    ]), 'UpdateConferenceParticipantCallIdJob:Call:save');
                }
            }

            $participant = $this->getParticipant($this->callSid, $this->conferenceSid);
            $participant->cp_call_id = $call->c_id;
            if (!$participant->save()) {
                throw new \DomainException('Participant saving error. Errors: ' .
                    VarDumper::dumpAsString($participant->getErrors()));
            }
            $this->sendSocketData($participant->cp_cf_id);
        } catch (\Throwable $e) {
            \Yii::error([
                'message' => 'Update Conference Participant Error',
                'error' => $e->getMessage(),
                'callSid' => $this->callSid,
                'conferenceSid' => $this->conferenceSid,
            ], 'UpdateConferenceParticipantCallIdJob');
        }
    }

    private function sendSocketData(int $conferenceId): void
    {
        if (!$data = ConferenceDataService::getDataById($conferenceId)) {
            return;
        }
        SocketCommands::sendToAllUsers($data);
    }

    private function getParticipant(string $callSid, string $conferenceSid): ConferenceParticipant
    {
        $participant = ConferenceParticipant::find()
            ->andWhere([
                'cp_call_sid' => $callSid,
                'cp_cf_sid' => $conferenceSid
            ])
            ->one();

        if (!$participant) {
            throw new \DomainException('Not found Conference participant.');
        }

        if ($participant->cp_call_id) {
            throw new \DomainException('Call ID already exists.');
        }

        return $participant;
    }

    private function getCall(string $callSid): Call
    {
        $call = Call::find()->andWhere(['c_call_sid' => $callSid])->one();
        if (!$call) {
            throw new \DomainException('Call SID: ' . $callSid . ' not found. Reason: Call callback not received after participant callback within ' . ConferenceStatusCallbackHandler::DELAY_JOB_FOR_UPDATE_PARTICIPANT_CALL_ID . ' seconds');
        }
        return $call;
    }
}

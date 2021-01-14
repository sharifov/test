<?php

namespace common\components\jobs;

use common\models\Call;
use common\models\ConferenceParticipant;
use sales\model\conference\service\ConferenceDataService;
use sales\model\conference\socket\SocketCommands;
use yii\helpers\VarDumper;
use yii\queue\JobInterface;

/**
 * Class UpdateConferenceParticipantCallIdJob
 *
 * @property string $callSid
 * @property string $conferenceSid
 */
class UpdateConferenceParticipantCallIdJob implements JobInterface
{
    public string $callSid;
    public string $conferenceSid;

    public function __construct(string $callSid, string $conferenceSid)
    {
        $this->callSid = $callSid;
        $this->conferenceSid = $conferenceSid;
    }

    public function execute($queue)
    {
        try {
            $callId = $this->getCallId($this->callSid);
            $participant = $this->getParticipant($this->callSid, $this->conferenceSid);
            $participant->cp_call_id = $callId;
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

    private function getCallId(string $callSid): int
    {
        $call = Call::find()->select(['c_id'])->andWhere(['c_call_sid' => $callSid])->asArray()->one();
        if (!$call) {
            throw new \DomainException('Call SID: ' . $callSid . ' not found.');
        }
        return $call['c_id'];
    }
}

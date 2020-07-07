<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use sales\model\conference\service\ConferenceDataService;
use Yii;
use yii\helpers\VarDumper;

class ConferenceParticipantLeave
{
    private Conference $conference;

    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
    }

    public function __invoke(ConferenceStatusCallbackForm $form)
    {
        $conference = $this->conference;

        $participant = ConferenceParticipant::find()->where([
            'cp_cf_id' => $conference->cf_id,
            'cp_call_sid' => $form->CallSid,
        ])->one();

        if ($participant) {
            $participant->leave(date('Y-m-d H:i:s'));
            if (!$participant->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'errors' => $participant->getErrors(),
                    'model' => $participant->getAttributes(),
                ]), static::class);
            }
            $this->sendMessageToSocket($participant);
            return;
        }

        $call = ConferenceParticipantCallFinder::findAndUpdateCall($form->CallSid, $conference);

        $participant = new ConferenceParticipant();
        $participant->cp_type_id = $form->participant_type_id;
        $participant->cp_cf_id = $conference->cf_id;
        $participant->cp_call_sid = $form->CallSid;
        $participant->leave(date('Y-m-d H:i:s'));
        if ($call) {
            $participant->cp_call_id = $call->c_id;
        }
        if (!$participant->save()) {
            Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), static::class);
        }
        $this->sendMessageToSocket($participant);
    }

    private function sendMessageToSocket(ConferenceParticipant $participant): void
    {
        if (!$data = ConferenceDataService::getDataById($participant->cp_cf_id)) {
            return;
        }

        foreach ($data['users'] as $userId) {

            $participants = [];
            foreach ($data['participants'] as $key => $part) {
                if (!$part['userId'] || $part['userId'] === $userId) {
                    unset($part['userId']);
                    $participants[] = $part;
                }
            }

            Notifications::publish('conferenceUpdate', ['user_id' => $userId],
                [
                    'data' => [
                        'command' => 'conferenceUpdate',
                        'conference' => [
                            'sid' => $data['conference']['sid'],
                            'duration' => $data['conference']['duration'],
                            'participants' => $participants,
                        ],
                    ]
                ]
            );
        }
    }
}

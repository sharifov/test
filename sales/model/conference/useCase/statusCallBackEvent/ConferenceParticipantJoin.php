<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use sales\model\conference\service\ConferenceDataService;
use sales\model\conference\socket\SocketCommands;
use Yii;
use yii\helpers\VarDumper;

class ConferenceParticipantJoin
{
    private Conference $conference;

    public function __construct(Conference $conference)
    {
        $this->conference = $conference;
    }

    public function __invoke(ConferenceStatusCallbackForm $form)
    {
        $conference = $this->conference;

        $call = ConferenceParticipantCallFinder::findAndUpdateCall($form->CallSid, $conference);

        $participant = new ConferenceParticipant();
        $participant->cp_type_id = $form->participant_type_id;
        $participant->cp_cf_id = $conference->cf_id;
        $participant->cp_call_sid = $form->CallSid;
        if ($call) {
            $participant->cp_call_id = $call->c_id;
        }
        $participant->join();
        $participant->cp_join_dt = date('Y-m-d H:i:s');
        if (!$participant->save()) {
            Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), static::class);
            return;
        }
        $this->sendMessageToSocket($participant);
    }

    private function sendMessageToSocket(ConferenceParticipant $participant): void
    {
        if (!$data = ConferenceDataService::getDataById($participant->cp_cf_id)) {
            return;
        }

        SocketCommands::sendToAllUsers($data);
    }
}

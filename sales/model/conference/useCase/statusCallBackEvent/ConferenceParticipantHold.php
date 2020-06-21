<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Call;
use common\models\Conference;
use common\models\ConferenceParticipant;
use common\models\Notifications;
use frontend\widgets\newWebPhone\call\socket\HoldMessage;
use Yii;
use yii\helpers\VarDumper;

class ConferenceParticipantHold
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
            $participant->hold(date('Y-m-d H:i:s'));
            if ($participant->save()) {
                if ($call = $participant->cpCall) {
                    $this->sendMessageToSocket($call, $participant);
                }
            } else {
                Yii::error(VarDumper::dumpAsString([
                    'errors' => $participant->getErrors(),
                    'model' => $participant->getAttributes(),
                ]), static::class);
            }
            return;
        }

        $call = ConferenceParticipantCallFinder::findAndUpdateCall($form->CallSid, $conference);

        $participant = new ConferenceParticipant();
        $participant->cp_cf_id = $conference->cf_id;
        $participant->cp_call_sid = $form->CallSid;
        $participant->hold(date('Y-m-d H:i:s'));
        if ($call) {
            $participant->cp_call_id = $call->c_id;
        }
        if ($participant->save()) {
            if ($call) {
                $this->sendMessageToSocket($call, $participant);
            }
        } else {
            Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), static::class);
        }
    }

    private function sendMessageToSocket(Call $call, ConferenceParticipant $participant): void
    {
        if ($call && $call->c_created_user_id && $participant->isAgent()) {
            Notifications::publish(HoldMessage::COMMAND, ['user_id' => $call->c_created_user_id], HoldMessage::hold($call));
        }
    }
}

<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Conference;
use common\models\ConferenceParticipant;
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
            $participant->leave();
            $participant->cp_leave_dt = date('Y-m-d H:i:s');
            if (!$participant->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'errors' => $participant->getErrors(),
                    'model' => $participant->getAttributes(),
                ]), static::class);
            }
            return;
        }

        $call = ConferenceParticipantCallFinder::findAndUpdateCall($form->CallSid, $conference);

        $participant = new ConferenceParticipant();
        $participant->cp_type_id = $form->participant_type_id;
        $participant->cp_cf_id = $conference->cf_id;
        $participant->cp_call_sid = $form->CallSid;
        $participant->leave();
        $participant->cp_leave_dt = date('Y-m-d H:i:s');
        if ($call) {
            $participant->cp_call_id = $call->c_id;
        }
        if (!$participant->save()) {
            Yii::error(VarDumper::dumpAsString([
                'errors' => $participant->getErrors(),
                'model' => $participant->getAttributes(),
            ]), static::class);
        }
    }
}

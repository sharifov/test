<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Conference;
use common\models\ConferenceParticipant;
use sales\model\conference\form\ConferenceStatusCallbackForm;
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
            $participant->hold();
            if (!$participant->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'errors' => $participant->getErrors(),
                    'model' => $participant->getAttributes(),
                ]), static::class);
            }
            return;
        }

        $call = ConferenceParticipantCallFinder::find($form->CallSid, $conference->cf_sid);

        $participant = new ConferenceParticipant();
        $participant->cp_cf_id = $conference->cf_id;
        $participant->cp_call_sid = $form->CallSid;
        $participant->hold();
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

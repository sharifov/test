<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Conference;
use common\models\ConferenceParticipant;
use sales\model\conference\form\ConferenceStatusCallbackForm;
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

        $call = ConferenceParticipantCallFinder::find($form->CallSid, $conference->cf_sid);

        $participant = new ConferenceParticipant();
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
        }
    }
}

<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Call;
use Yii;
use yii\helpers\VarDumper;

class ConferenceParticipantCallFinder
{
    public static function find($callSid, $conferenceSid): ?Call
    {
        if (!$call = Call::find()->where(['c_call_sid' => $callSid])->one()) {
            return null;
        }

        if ($call->c_conference_sid !== $conferenceSid) {
            $call->c_conference_sid = $conferenceSid;
            if (!$call->save()) {
                Yii::error(VarDumper::dumpAsString([
                    'errors' => $call->getErrors(),
                    'model' => $call->getAttributes(),
                ]),static::class);
            }
        }

        return $call;
    }
}

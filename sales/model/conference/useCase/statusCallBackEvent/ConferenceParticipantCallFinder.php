<?php

namespace sales\model\conference\useCase\statusCallBackEvent;

use common\models\Call;
use common\models\Conference;
use Yii;
use yii\helpers\VarDumper;

class ConferenceParticipantCallFinder
{
    public static function findAndUpdateCall($callSid, Conference $conference): ?Call
    {
        if (!$call = Call::find()->where(['c_call_sid' => $callSid])->one()) {
            return null;
        }

        if ($call->c_conference_id !== $conference->cf_id) {
            $call->c_conference_sid = $conference->cf_sid;
            $call->c_conference_id = $conference->cf_id;
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

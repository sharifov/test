<?php

namespace src\model\callLogFilterGuard\service;

use common\components\antispam\CallAntiSpamDto;
use common\models\Call;
use DomainException;
use frontend\helpers\JsonHelper;
use src\helpers\ErrorsToStringHelper;
use src\model\callLogFilterGuard\entity\CallLogFilterGuard;
use src\model\callLogFilterGuard\repository\CallLogFilterGuardRepository;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\services\phone\callFilterGuard\TwilioCallFilterGuard;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class CallLogFilterGuardService
 */
class CallLogFilterGuardService
{
    public function handler(string $phoneNumber, Call $call): CallLogFilterGuard
    {
        if ($callLogFilterGuard = CallLogFilterGuard::findOne(['clfg_call_id' => $call->c_id])) {
            return $callLogFilterGuard;
        }

        $twilioCallFilterGuard = (new TwilioCallFilterGuard($phoneNumber))->default();

        if (empty($dataJson = $twilioCallFilterGuard->response)) {
            throw new DomainException('TwilioCallFilterGuard Error: ResponseData is empty');
        }

        $dto = CallAntiSpamDto::fillFromCallTwilioResponse($dataJson, $call);
        $response = Yii::$app->callAntiSpam->checkData($dto);

        if (!empty($response['error'])) {
            throw new DomainException(VarDumper::dumpAsString($response['error']));
        }
        if (($label = $response['data']['Label'] ?? null) === null || ($score = $response['data']['Score'] ?? null) === null) {
            throw new DomainException('CallAntiSpamService Error: Label and Score is required in response');
        }

        $contactPhoneList = ContactPhoneListService::getByPhone($phoneNumber);
        $callLogFilterGuard = CallLogFilterGuard::create(
            $call->c_id,
            $label,
            $score,
            $twilioCallFilterGuard->getTrustPercent(),
            $contactPhoneList->cpl_id ?? null
        );

        if (!$callLogFilterGuard->validate()) {
            throw new DomainException(ErrorsToStringHelper::extractFromModel($callLogFilterGuard));
        }

        (new CallLogFilterGuardRepository($callLogFilterGuard))->save();

        $dataJson = JsonHelper::decode($call->c_data_json);
        $dataJson['callAntiSpamData'] = [
            'type' => $callLogFilterGuard->clfg_type,
            'rate' => $callLogFilterGuard->clfg_sd_rate,
            'trustPercent' => $callLogFilterGuard->clfg_trust_percent,
        ];
        $call->c_data_json = JsonHelper::encode($dataJson);
        $call->save(false);

        return $callLogFilterGuard;
    }

    public static function checkCallLog(?int $callId): ?CallLogFilterGuard
    {
        return CallLogFilterGuard::find()
            ->where(['clfg_call_id' => $callId])
            ->andWhere(['IS', 'clfg_call_log_id', null])
            ->one();
    }
}

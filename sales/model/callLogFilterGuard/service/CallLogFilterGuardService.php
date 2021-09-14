<?php

namespace sales\model\callLogFilterGuard\service;

use common\components\antispam\CallAntiSpamDto;
use common\models\Call;
use frontend\helpers\JsonHelper;
use sales\helpers\ErrorsToStringHelper;
use sales\model\callLogFilterGuard\entity\CallLogFilterGuard;
use sales\model\callLogFilterGuard\repository\CallLogFilterGuardRepository;
use sales\model\contactPhoneList\service\ContactPhoneListService;
use sales\services\phone\callFilterGuard\TwilioCallFilterGuard;
use Yii;
use yii\helpers\VarDumper;

/**
 * Class CallLogFilterGuardService
 */
class CallLogFilterGuardService
{
    public function handler(string $phoneNumber, Call $call): CallLogFilterGuard
    {
        $twilioCallFilterGuard = (new TwilioCallFilterGuard($phoneNumber))->default();

        if (empty($dataJson = $twilioCallFilterGuard->getResponseData())) {
            throw new \RuntimeException('TwilioCallFilterGuard Error: ResponseData is empty');
        }

        $dto = CallAntiSpamDto::fillFromCallTwilioResponse($dataJson, $call);
        $response = Yii::$app->callAntiSpam->checkData($dto);

        if (!empty($response['error'])) {
            throw new \RuntimeException(VarDumper::dumpAsString($response['error']));
        }
        if (($label = $response['data']['Label'] ?? null) === null || ($score = $response['data']['Score'] ?? null) === null) {
            throw new \RuntimeException('CallAntiSpamService Error: Label and Score is required in response');
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
            throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($callLogFilterGuard));
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
}

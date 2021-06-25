<?php

namespace sales\services\phone\callFilterGuard;

use yii\helpers\ArrayHelper;

/**
 * Class TwilioCallFilterGuard
 *
 * @property string $phone
 * @property int $trustPercent
 * @property array|null $response
 */
class TwilioCallFilterGuard
{
    private string $phone;
    private int $trustPercent = 0;
    private $response;

    public function __construct(string $phone)
    {
        $this->phone = $phone;
    }

    public function checkPhone(): int
    {
        $this->response = \Yii::$app->communication->lookup($this->phone);

        $type = ArrayHelper::getValue($this->response, 'result.result.carrier.type');
        $mobile_country_code = ArrayHelper::getValue($this->response, 'result.result.carrier.mobile_country_code');
        $mobile_network_code = ArrayHelper::getValue($this->response, 'result.result.carrier.mobile_network_code');

        if ($type === 'voip') {
            $this->trustPercent = 0;
        } else {
            $this->trustPercent = 100;
        }

        return $this->trustPercent;
    }

    public function checkPhoneOld(): int
    {
        $this->response = \Yii::$app->communication->lookup($this->phone);

        $type = ArrayHelper::getValue($this->response, 'result.result.carrier.type');
        $mobile_country_code = ArrayHelper::getValue($this->response, 'result.result.carrier.mobile_country_code');
        $mobile_network_code = ArrayHelper::getValue($this->response, 'result.result.carrier.mobile_network_code');

        if ($type === 'voip' && $mobile_country_code === null && $mobile_network_code === null) {
            $this->trustPercent = 0;
        } else {
            $this->trustPercent = 100;
        }

        return $this->trustPercent;
    }

    public function getTrustPercent(): int
    {
        return $this->trustPercent;
    }
}

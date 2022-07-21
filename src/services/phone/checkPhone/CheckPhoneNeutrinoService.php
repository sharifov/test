<?php

namespace src\services\phone\checkPhone;

use src\helpers\app\AppHelper;
use src\helpers\setting\SettingHelper;
use common\components\CommunicationService;
use Yii;
use yii\helpers\ArrayHelper;

/**
 * Class CheckPhoneNeutrinoService
 *
 * @property string|null $phone
 * @property bool $isEnableCheckPhoneByApi
 * @property CommunicationService $communicationService
 * @property bool $isRequestChecked
 */
class CheckPhoneNeutrinoService
{
    private string $phone;
    private bool $isEnableCheckPhoneByApi;
    private CommunicationService $communicationService;
    private bool $isRequestChecked = false;

    /**
     * @param string $phone
     * @param bool|null $isEnableCheckPhoneByApi
     */
    public function __construct(string $phone, ?bool $isEnableCheckPhoneByApi = null)
    {
        $this->phone = $phone;
        $this->isEnableCheckPhoneByApi = $isEnableCheckPhoneByApi ?? SettingHelper::isEnableCheckPhoneByNeutrino();
        $this->communicationService = Yii::$app->comms;
    }

    public function checkRequest(): ?array
    {
        if (!$this->isEnableCheckPhoneByApi) {
            return null;
        }
        return $this->getRequest();
    }

    public function getRequest(): ?array
    {
        $response = $this->communicationService->checkPhoneNumber($this->phone);
        if ($error = ArrayHelper::getValue($response, 'error')) {
            throw new \RuntimeException($error);
        }
        if ($result = ArrayHelper::getValue($response, 'result')) {
            $this->isRequestChecked = true;
            return $result;
        }
        throw new \RuntimeException('Response contains no data');
    }

    public function isEnableCheckPhoneByApi(): bool
    {
        return $this->isEnableCheckPhoneByApi;
    }
}

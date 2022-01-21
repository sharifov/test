<?php

namespace common\components;

use common\components\jobs\BaseJob;
use src\helpers\app\AppHelper;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use src\model\contactPhoneServiceInfo\repository\ContactPhoneServiceInfoRepository;
use src\model\contactPhoneServiceInfo\service\ContactPhoneInfoService;
use src\services\phone\checkPhone\CheckPhoneNeutrinoService;
use src\services\phone\checkPhone\CheckPhoneService;
use common\models\ClientPhone;
use yii\helpers\ArrayHelper;

/**
 * Class CheckPhoneNumberJob
 * @property $client_id
 * @property $client_phone_id
 */
class CheckPhoneNumberJob extends BaseJob implements \yii\queue\JobInterface
{
    public $client_id = 0;
    public $client_phone_id = 0;

    public function execute($queue)
    {
        $this->waitingTimeRegister();

        try {
            if (!$this->client_id || !$this->client_phone_id) {
                throw new \RuntimeException('Error CheckPhoneNumberJob: (client_id < 1 || client_phone_id < 1)');
            }
            $clientPhone = ClientPhone::findOne(['id' => $this->client_phone_id]);
            if (!$clientPhone) {
                throw new \RuntimeException('Error CheckPhoneNumberJob: ClientPhone not found in db (id: ' . $this->client_phone_id . ')');
            }
            if (strlen($clientPhone->phone) < 8) {
                throw new \RuntimeException('Error CheckPhoneNumberJob: ClientPhone is < 8 (' . $clientPhone->phone . ', clientId: ' . $clientPhone->client_id . ')');
            }

            $checkPhoneNeutrinoService = new CheckPhoneNeutrinoService($clientPhone->phone);
            if ($numbers = $checkPhoneNeutrinoService->checkRequest()) {
                foreach ($numbers as $phoneNumber => $phoneData) {
                    $phone = CheckPhoneService::cleanPhone($phoneNumber);

                    if (
                        isset($phoneData['internationalNumber'], $phoneData['numberType']) &&
                        ($phone === $clientPhone->phone) &&
                        !count($phoneData['errors'])
                    ) {
                        $clientPhone->updateAttributes([
                            'is_sms' => ($phoneData['numberType'] === 'mobile') ? 1 : 0,
                            'validate_dt' => date('Y-m-d H:i:s'),
                            'cp_cpl_uid' => CheckPhoneService::uidGenerator($phone)
                        ]);
                    }

                    $contactPhoneList = ContactPhoneListService::getOrCreate($phone);
                    ContactPhoneInfoService::getOrCreate(
                        $contactPhoneList->cpl_id,
                        ContactPhoneServiceInfo::SERVICE_NEUTRINO,
                        $phoneData
                    );
                }
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                'clientPhoneId' => $this->client_phone_id,
                'clientId' => $this->client_id,
            ]);
            \Yii::warning($message, 'CheckPhoneNumberJob:Exception');
        } catch (\Throwable $throwable) {
            \Yii::error(AppHelper::throwableLog($throwable), 'CheckPhoneNumberJob:Throwable');
        }
    }
}

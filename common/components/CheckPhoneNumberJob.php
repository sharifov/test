<?php

namespace common\components;

use common\components\jobs\BaseJob;
use sales\helpers\app\AppHelper;
use sales\model\contactPhoneList\service\ContactPhoneListService;
use sales\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use sales\model\contactPhoneServiceInfo\repository\ContactPhoneServiceInfoRepository;
use sales\model\contactPhoneServiceInfo\service\ContactPhoneInfoService;
use sales\services\phone\checkPhone\CheckPhoneNeutrinoService;
use sales\services\phone\checkPhone\CheckPhoneService;
use common\models\ClientPhone;

/**
 * Class CheckPhoneNumberJob
 * @property $client_id
 * @property $client_phone_id
 */
class CheckPhoneNumberJob extends BaseJob implements \yii\queue\JobInterface
{
    public $client_id = 0;
    public $client_phone_id = 0;

    /**
     * @param \yii\queue\Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        $this->waitingTimeRegister();

        try {
            if (!$this->client_id || !$this->client_phone_id) {
                throw new \Exception('Error CheckPhoneNumberJob: (client_id < 1 || client_phone_id < 1)');
            }
            $clientPhone = ClientPhone::findOne(['client_id' => $this->client_id, 'id' => $this->client_phone_id ]);
            if (!$clientPhone) {
                throw new \Exception('Error CheckPhoneNumberJob: ClientPhone not found in db (client_id: ' . $this->client_id . ', phone_id: ' . $this->client_phone_id . ')');
            }
            if (strlen($clientPhone->phone) < 8) {
                throw new \Exception('Error CheckPhoneNumberJob: ClientPhone is < 8 (' . $clientPhone->phone . ', clientId: ' . $clientPhone->client_id . ')');
            }

            $checkPhoneNeutrinoService = new CheckPhoneNeutrinoService($clientPhone->phone);
            if ($numbers = $checkPhoneNeutrinoService->checkRequest()) {
                foreach ($numbers as $phoneNumber => $phoneData) {
                    $phone = CheckPhoneService::cleanPhone($phoneNumber);

                    if (isset($phoneData['internationalNumber'], $phoneData['numberType'])) {
                        if (($phone === $clientPhone->phone) && !count($phoneData['errors'])) {
                            $clientPhone->updateAttributes([
                                'is_sms' => ($phoneData['numberType'] === 'mobile') ? 1 : 0,
                                'validate_dt' => date('Y-m-d H:i:s'),
                                'cp_cpl_uid' => CheckPhoneService::uidGenerator($phone)
                            ]);
                        }
                    }

                    $contactPhoneList = ContactPhoneListService::getOrCreate($phone);
                    ContactPhoneInfoService::getOrCreate(
                        $contactPhoneList->cpl_id,
                        ContactPhoneServiceInfo::SERVICE_NEUTRINO,
                        $phoneData
                    );
                }
            }

            return true;
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'CheckPhoneNumberJob:execute');
        }
        return true;
    }
}

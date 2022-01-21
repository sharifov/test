<?php

namespace common\components;

use borales\extensions\phoneInput\PhoneInputValidator;
use common\components\jobs\BaseJob;
use common\models\ClientPhone;
use src\helpers\app\AppHelper;
use src\model\contactPhoneList\service\ContactPhoneListService;
use src\model\contactPhoneServiceInfo\entity\ContactPhoneServiceInfo;
use src\model\contactPhoneServiceInfo\repository\ContactPhoneServiceInfoRepository;
use src\model\contactPhoneServiceInfo\service\ContactPhoneInfoService;
use src\services\phone\checkPhone\CheckPhoneNeutrinoService;
use src\services\phone\checkPhone\CheckPhoneService;

/**
 * Class CheckPhoneNumberJob
 * @property $phone
 * @property string|null $title
 */
class CheckPhoneByNeutrinoJob implements \yii\queue\JobInterface
{
    public $phone;
    public ?string $title = null;

    /**
     * @param \yii\queue\Queue $queue
     * @return bool
     */
    public function execute($queue): bool
    {
        try {
            $validator = new PhoneInputValidator();
            if (!$validator->validate($this->phone)) {
                throw new \RuntimeException('Phone(' . $this->phone . ') not valid');
            }

            $checkPhoneNeutrinoService = new CheckPhoneNeutrinoService($this->phone);
            if ($numbers = $checkPhoneNeutrinoService->checkRequest()) {
                foreach ($numbers as $phoneNumber => $phoneData) {
                    $phone = CheckPhoneService::cleanPhone($phoneNumber);
                    $contactPhoneList = ContactPhoneListService::getOrCreate($phone, $this->title);

                    ContactPhoneInfoService::getOrCreate(
                        $contactPhoneList->cpl_id,
                        ContactPhoneServiceInfo::SERVICE_NEUTRINO,
                        $phoneData
                    );
                }

                ClientPhone::updateAll(
                    ['cp_cpl_uid' => CheckPhoneService::uidGenerator($this->phone)],
                    ['phone' => $this->phone]
                );
            }

            return true;
        } catch (\Throwable $e) {
            \Yii::error(AppHelper::throwableLog($e), 'CheckPhoneByNeutrinoJob:execute');
        }
        return true;
    }
}

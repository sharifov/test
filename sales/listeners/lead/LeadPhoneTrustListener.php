<?php

namespace sales\listeners\lead;

use common\models\ClientPhone;
use sales\events\lead\LeadableEventInterface;
use sales\model\contactPhoneData\service\ContactPhoneDataDictionary;
use sales\model\contactPhoneData\service\ContactPhoneDataService;
use sales\model\contactPhoneList\service\ContactPhoneListService;
use Yii;

/**
 * Class LeadPhoneTrustListener
 */
class LeadPhoneTrustListener
{
    public function handle(LeadableEventInterface $event): void
    {
        try {
            $lead = $event->getLead();
            $phones = $lead->client->getClientPhonesByType(
                [
                    null,
                    ClientPhone::PHONE_VALID,
                    ClientPhone::PHONE_NOT_SET,
                    ClientPhone::PHONE_FAVORITE,
                ]
            );

            if ($phones) {
                foreach ($phones as $phone) {
                    $contactPhoneList = ContactPhoneListService::getOrCreate($phone, 'Is trusted');
                    ContactPhoneDataService::getOrCreate(
                        $contactPhoneList->cpl_id,
                        ContactPhoneDataDictionary::KEY_IS_TRUSTED,
                        '1'
                    );
                }
            }
        } catch (\Throwable $e) {
            Yii::error($e, 'Listeners:LeadPhoneTrustListener');
        }
    }
}

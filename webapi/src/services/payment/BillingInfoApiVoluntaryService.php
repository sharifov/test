<?php

namespace webapi\src\services\payment;

use common\models\BillingInfo;
use src\dto\billingInfo\BillingInfoDTO;
use src\repositories\billingInfo\BillingInfoRepository;
use webapi\src\forms\billing\BillingInfoForm;

/**
 * Class BillingInfoApiVoluntaryService
 */
class BillingInfoApiVoluntaryService
{
    public static function getOrCreateBillingInfo(
        BillingInfoForm $form,
        ?int $orderId,
        ?int $creditCardId,
        ?int $paymentMethodId
    ): BillingInfo {
        if ($billingInfo = self::findBillingInfo($form, $orderId, $creditCardId)) {
            return $billingInfo;
        }
        return self::createBillingInfo($form, $orderId, $creditCardId, $paymentMethodId);
    }

    public static function findBillingInfo(BillingInfoForm $form, ?int $orderId, ?int $creditCardId = null): ?BillingInfo
    {
        return BillingInfo::find()
            ->where(['bi_first_name' => $form->first_name])
            ->andWhere(['bi_last_name' => $form->last_name])
            ->andWhere(['bi_middle_name' => $form->middle_name])
            ->andWhere(['bi_address_line1' => $form->address_line1])
            ->andWhere(['bi_address_line2' => $form->address_line2])
            ->andWhere(['bi_city' => $form->city])
            ->andWhere(['bi_state' => $form->state])
            ->andWhere(['bi_country' => $form->country_id])
            ->andWhere(['bi_zip' => $form->zip])
            ->andWhere(['bi_contact_phone' => $form->contact_phone])
            ->andWhere(['bi_contact_email' => $form->contact_email])
            ->andWhere(['bi_order_id' => $orderId])
            ->andWhere(['bi_cc_id' => $creditCardId])
            ->one();
    }

    public static function createBillingInfo(BillingInfoForm $form, ?int $orderId, ?int $creditCardId, ?int $paymentMethodId): BillingInfo
    {
        $billingInfoDTO = (new BillingInfoDTO())->fillByVoluntaryForm($form, $orderId, $creditCardId, $paymentMethodId);
        $billingInfo = BillingInfo::createByDto($billingInfoDTO);
        (new BillingInfoRepository())->save($billingInfo);
        return $billingInfo;
    }
}

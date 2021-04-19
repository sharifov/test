<?php

namespace webapi\src\services\payment;

use common\models\BillingInfo;
use common\models\CreditCard;
use modules\order\src\forms\api\create\BillingInfoForm;

/**
 * Class BillingInfoApiService
 */
class BillingInfoApiService
{
    public static function getOrCreateBillingInfo(BillingInfoForm $form, int $orderId, ?int $creditCardId): BillingInfo
    {
        if (
            $billingInfo = BillingInfo::find()
                ->where(['bi_first_name' => $form->first_name])
                ->andWhere(['bi_last_name' => $form->last_name])
                ->andWhere(['bi_middle_name' => $form->middle_name])
                ->andWhere(['bi_address_line1' => $form->address])
                ->andWhere(['bi_city' => $form->city])
                ->andWhere(['bi_state' => $form->state])
                ->andWhere(['bi_country' => $form->country_id])
                ->andWhere(['bi_zip' => $form->zip])
                ->andWhere(['bi_contact_phone' => $form->phone])
                ->andWhere(['bi_contact_email' => $form->email])
                ->andWhere(['bi_order_id' => $orderId])
                ->andWhere(['bi_cc_id' => $creditCardId])
                ->one()
        ) {
            return $billingInfo;
        }
        return BillingInfo::create(
            $form->first_name,
            $form->last_name,
            $form->middle_name,
            $form->address,
            $form->city,
            $form->state,
            $form->country_id,
            $form->zip,
            $form->phone,
            $form->email,
            null,
            $creditCardId,
            $orderId
        );
    }

    public static function existBillingInfo(BillingInfoForm $form, int $orderId): bool
    {
        return BillingInfo::find()
            ->where(['bi_first_name' => $form->first_name])
            ->andWhere(['bi_last_name' => $form->last_name])
            ->andWhere(['bi_middle_name' => $form->middle_name])
            ->andWhere(['bi_address_line1' => $form->address])
            ->andWhere(['bi_city' => $form->city])
            ->andWhere(['bi_state' => $form->state])
            ->andWhere(['bi_country' => $form->country_id])
            ->andWhere(['bi_zip' => $form->zip])
            ->andWhere(['bi_contact_phone' => $form->phone])
            ->andWhere(['bi_contact_email' => $form->email])
            ->andWhere(['bi_order_id' => $orderId])
            ->exists();
    }

    public static function createBillingInfo(BillingInfoForm $form, ?int $creditCardId, int $orderId): BillingInfo
    {
        return BillingInfo::create(
            $form->first_name,
            $form->last_name,
            $form->middle_name,
            $form->address,
            $form->city,
            $form->state,
            $form->country_id,
            $form->zip,
            $form->phone,
            $form->email,
            null,
            $creditCardId,
            $orderId
        );
    }
}

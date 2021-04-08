<?php

namespace webapi\src\services\payment;

use common\models\CreditCard;
use modules\order\src\forms\api\create\CreditCardForm;

/**
 * Class CreditCardApiService
 */
class CreditCardApiService
{
    public static function existCreditCard(CreditCardForm $form): bool
    {
        return CreditCard::find()
            ->where(['cc_holder_name' => $form->holder_name])
            ->andWhere(['cc_type_id' => $form->type_id])
            ->andWhere(['cc_expiration_month' => $form->expiration_month])
            ->andWhere(['cc_expiration_year' => $form->expiration_year])
            ->andWhere(['cc_type_id' => $form->type_id])
            ->exists();
    }

    public static function createCreditCard(CreditCardForm $form): CreditCard
    {
        $creditCard = CreditCard::create(
            $form->number,
            $form->holder_name,
            $form->expiration_month,
            $form->expiration_year,
            $form->cvv,
            $form->type_id
        );
        $creditCard->updateSecureCardNumber();
        $creditCard->updateSecureCvv();
        return $creditCard;
    }
}
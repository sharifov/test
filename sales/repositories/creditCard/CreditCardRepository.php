<?php

namespace sales\repositories\creditCard;

use common\models\CreditCard;

class CreditCardRepository
{
    /**
     * @param int $saleId
     * @return array
     */
    public function findBySaleId(int $saleId): array
    {
        return CreditCard::find()->innerJoin('sale_credit_card', 'scc_cc_id=cc_id')->where(['scc_sale_id' => $saleId])->all();
    }

    public function save(CreditCard $creditCard): void
    {
        if (!$creditCard->save(false)) {
            throw new \RuntimeException($creditCard->getErrorSummary(false)[0]);
        }
    }
}

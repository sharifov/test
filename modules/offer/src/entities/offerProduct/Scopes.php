<?php

namespace modules\offer\src\entities\offerProduct;

/**
 * @see OfferProduct
 */
class Scopes extends \yii\db\ActiveQuery
{
    public function byOfferId(int $id): Scopes
    {
        return $this->andWhere(['op_offer_id' => $id]);
    }
}

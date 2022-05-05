<?php

namespace common\models\query;

use common\models\CaseSale;
use yii\db\Expression;

/**
 * This is the ActiveQuery class for [[CaseSale]].
 *
 * @see CaseSale
 */
class CaseSaleQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * {@inheritdoc}
     * @return CaseSale[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * {@inheritdoc}
     * @return CaseSale|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }

    public function byBaseBookingId(string $bookingId): CaseSaleQuery
    {
        $expression = new Expression(
            'JSON_VALUE(css_sale_data, "$.baseBookingId") = :baseBookingId',
            [':baseBookingId' => $bookingId]
        );
        $this->andWhere([
            'OR',
            ['css_sale_book_id' => $bookingId],
            $expression
        ]);

        return $this;
    }
}

<?php

namespace modules\flight\coupon;

use sales\model\coupon\entity\couponProduct\service\AbstractCouponProduct;

/**
 * Class CouponFlight
 * @property $departure_airport_iata
 * @property $arrival_airport_iata
 * @property $marketing_airline
 * @property $cabin_class
 */
class CouponFlight extends AbstractCouponProduct
{
    public $departure_airport_iata;
    public $arrival_airport_iata;
    public $marketing_airline;
    public $cabin_class;

    public function rules(): array
    {
        return [
            ['departure_airport_iata', 'string', 'max' => 3],

            ['arrival_airport_iata', 'string', 'max' => 3],

            ['marketing_airline', 'string', 'max' => 2],

            ['cabin_class', 'string', 'max' => 2],
        ];
    }
}

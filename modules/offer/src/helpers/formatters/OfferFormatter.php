<?php

namespace modules\offer\src\helpers\formatters;

use modules\offer\src\entities\offer\Offer;
use yii\bootstrap4\Html;

class OfferFormatter
{
    public static function asOffer(Offer $offer): string
    {
        return Html::a(
            'offer: ' . $offer->of_id,
            ['/offer/offer-crud/view', 'id' => $offer->of_id],
            ['target' => '_blank', 'data-pjax' => 0]
        );
    }
}

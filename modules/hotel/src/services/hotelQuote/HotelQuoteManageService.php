<?php

namespace modules\hotel\src\services\hotelQuote;

use modules\hotel\models\Hotel;
use modules\hotel\models\HotelList;
use modules\hotel\models\HotelQuote;
use modules\hotel\src\exceptions\HotelCodeException;
use modules\order\src\exceptions\OrderC2BDtoException;
use modules\order\src\exceptions\OrderC2BException;
use modules\order\src\forms\api\createC2b\QuotesForm;
use modules\product\src\interfaces\Productable;
use modules\product\src\interfaces\ProductQuoteService;
use yii\helpers\Json;

class HotelQuoteManageService implements ProductQuoteService
{
    /**
     * @param Productable|Hotel $product
     * @param QuotesForm $form
     * @throws \yii\base\InvalidConfigException
     */
    public function c2bHandle(Productable $product, QuotesForm $form): void
    {
        try {
            $hotelData = Json::decode($form->originSearchData);

            $currency = $hotelData['currency'] ?? 'USD';

            $hotelModel = HotelList::findOrCreateByData($hotelData);
            HotelQuote::findOrCreateByData($hotelData, $hotelModel, $product, null, $currency);
        } catch (\Throwable $e) {
            $dto = new OrderC2BDtoException(
                $product,
                $form->quoteOtaId
            );
            throw new OrderC2BException($dto, $e->getMessage(), HotelCodeException::API_C2B_HANDLE);
        }
    }
}

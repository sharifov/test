<?php

namespace modules\rentCar\src\services;

use modules\flight\src\exceptions\FlightCodeException;
use modules\order\src\exceptions\OrderC2BDtoException;
use modules\order\src\exceptions\OrderC2BException;
use modules\order\src\forms\api\createC2b\QuotesForm;
use modules\product\src\interfaces\Productable;
use modules\product\src\interfaces\ProductQuoteService;
use modules\rentCar\src\entity\dto\RentCarProductQuoteDto;
use modules\rentCar\src\entity\dto\RentCarQuoteDto;
use modules\rentCar\src\entity\rentCar\RentCar;
use modules\rentCar\src\exceptions\RentCarCodeException;
use sales\helpers\ErrorsToStringHelper;
use yii\helpers\Json;

class RentCarQuoteManageService implements ProductQuoteService
{
    /**
     * @param Productable|RentCar $product
     * @param QuotesForm $form
     */
    public function c2bHandle(Productable $product, QuotesForm $form): void
    {
        return;
        try {
            $quoteData = Json::decode($form->originSearchData);

            $productQuote = RentCarProductQuoteDto::create($product, $quoteData, null);
            if (!$productQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
            }

            $rentCarQuote = RentCarQuoteDto::create(
                $quoteData,
                $productQuote->pq_id,
                $product
            );
            if (!$rentCarQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($rentCarQuote));
            }

            $prices = (new RentCarQuotePriceCalculator())->calculate($rentCarQuote, $productQuote->pq_origin_currency_rate);
            $productQuote->updatePrices(
                $prices['originPrice'],
                $prices['appMarkup'],
                $prices['agentMarkup']
            );
            if (!$productQuote->save()) {
                throw new \RuntimeException(ErrorsToStringHelper::extractFromModel($productQuote));
            }
        } catch (\Throwable $e) {
            $dto = new OrderC2BDtoException(
                $product,
                $form->quoteOtaId
            );
            throw new OrderC2BException($dto, $e->getMessage(), RentCarCodeException::API_C2B_HANDLE);
        }
    }
}

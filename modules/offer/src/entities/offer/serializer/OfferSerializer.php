<?php

namespace modules\offer\src\entities\offer\serializer;

use modules\offer\src\entities\offer\Offer;
use sales\entities\serializer\Serializer;

/**
 * Class OfferExtraData
 *
 * @property Offer $model
 */
class OfferSerializer extends Serializer
{
    public function __construct(Offer $model)
    {
        parent::__construct($model);
    }

    public static function fields(): array
    {
        return [
            //'of_id',
            'of_gid',
            'of_uid',
            'of_name',
            'of_lead_id',
            'of_status_id',
//            'of_owner_user_id',
//            'of_created_user_id',
//            'of_updated_user_id',
//            'of_created_dt',
//            'of_updated_dt',
            'of_client_currency',
            'of_client_currency_rate',
            'of_app_total',
            'of_client_total',
        ];
    }

    public function getData(): array
    {
        $data = $this->toArray();

        $offerProducts = $this->model->offerProducts;

        if ($offerProducts) {
            foreach ($offerProducts as $offerProduct) {
                if ($quote = $offerProduct->opProductQuote) {

                    $quoteData = $quote->serialize();
                    $quoteData['product'] = $quote->pqProduct->serialize();

                    $productQuoteOptions = $quote->productQuoteOptions;
                    $productQuoteOptionsData = [];

                    if ($productQuoteOptions) {
                        foreach ($productQuoteOptions as $productQuoteOption) {
                            $productQuoteOptionsData[] = $productQuoteOption->serialize();
                        }
                    }

                    //$quoteData['productQuoteData'] = $quote->serialize();
                    $quoteData['productQuoteOptions'] = $productQuoteOptionsData;

                    $data['quotes'][] = $quoteData;
                    //$sum += $quote->totalCalcSum + $quote->pq_service_fee_sum;
                }
            }
            //$sum = round($sum, 2);
        }

        return $data;
    }
}

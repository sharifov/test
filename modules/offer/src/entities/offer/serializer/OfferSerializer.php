<?php

namespace modules\offer\src\entities\offer\serializer;

use modules\offer\src\entities\offer\Offer;
use modules\offer\src\entities\offer\OfferStatus;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteQuery;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelation;
use modules\product\src\entities\productQuoteRelation\ProductQuoteRelationQuery;
use src\entities\serializer\Serializer;
use src\model\leadData\services\LeadDataService;
use yii\helpers\ArrayHelper;

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
        $data['of_status_name'] = OfferStatus::getName($this->model->of_status_id);

        if ($offerProducts = $this->model->offerProducts) {
            foreach ($offerProducts as $offerProduct) {
                if ($quote = $offerProduct->opProductQuote) {
                    $quoteData = $quote->serialize();
                    $quoteData['product'] = $quote->pqProduct->serialize();

                    $productQuoteOptionsData = [];

                    if ($productQuoteOptions = $quote->productQuoteOptions) {
                        foreach ($productQuoteOptions as $productQuoteOption) {
                            $productQuoteOptionsData[] = $productQuoteOption->serialize();
                        }
                    }

                    //$quoteData['productQuoteData'] = $quote->serialize();
                    $quoteData['productQuoteOptions'] = $productQuoteOptionsData;

                    if ($productQuoteOrigin = ProductQuoteQuery::getOriginProductQuoteByAlternative($quote->pq_id)) {
                        $quoteData['origin'] = $productQuoteOrigin->serialize();
                        $quoteData['origin']['product'] = $productQuoteOrigin->pqProduct->serialize();

                        $productQuoteOriginOptionsData = [];

                        if ($productQuoteOptions = $productQuoteOrigin->productQuoteOptions) {
                            foreach ($productQuoteOptions as $productQuoteOption) {
                                $productQuoteOriginOptionsData[] = $productQuoteOption->serialize();
                            }
                        }
                        $quoteData['origin']['productQuoteOptions'] = $productQuoteOriginOptionsData;
                    }
                    $data['quotes'][] = $quoteData;

                    //$sum += $quote->totalCalcSum + $quote->pq_service_fee_sum;
                }
            }
            //$sum = round($sum, 2);
        }

        ArrayHelper::setValue(
            $data,
            'lead.lead_data',
            LeadDataService::getByLeadForApi($this->model->ofLead)
        );

        return $data;
    }
}

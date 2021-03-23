<?php

namespace modules\cruise\src\useCase\createQuote;

use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use modules\product\src\entities\productType\ProductType;
use modules\product\src\entities\productTypePaymentMethod\ProductTypePaymentMethodQuery;
use sales\helpers\product\ProductQuoteHelper;
use yii\helpers\VarDumper;

class CreateQuoteService
{
    public const SERVICE_FEE = 0.035;

    public function create(int $userId, array $quote, Cruise $cruise, $currency): int
    {
        $hashKey = $this->getHash($quote['id'] . $quote['cabin']['code']);

        $isExist = CruiseQuote::find()->where([
            'crq_cruise_id' => $cruise->crs_id,
            'crq_hash_key' => $hashKey
        ])->exists();

        if ($isExist) {
            throw new \DomainException('This quote already exists.');
        }

        $totalAmount = $quote['cabin']['price'];
        $transaction = \Yii::$app->db->beginTransaction();
        try {
            $productQuoteDto = new CruiseProductQuoteCreateDto();
            $productQuoteDto->productId = $cruise->crs_product_id;
            $productQuoteDto->originCurrency = $currency;
            $productQuoteDto->clientCurrency = ProductQuoteHelper::getClientCurrencyCode($cruise->product);
            $productQuoteDto->ownerUserId = $userId;
            $productQuoteDto->price = (float)$totalAmount;
            $productQuoteDto->originPrice = (float)$totalAmount;
            $productQuoteDto->clientPrice = (float)$totalAmount;
            $productQuoteDto->serviceFeeSum = 0;
            $productQuoteDto->clientCurrencyRate = ProductQuoteHelper::getClientCurrencyRate($cruise->product);
            $productQuoteDto->originCurrencyRate = 1;
            $productQuoteDto->name = $quote['cabin']['code'];

            $productTypeServiceFee = null;
            $productType = ProductType::find()->select(['pt_service_fee_percent'])->byCruise()->asArray()->one();
            if ($productType && $productType['pt_service_fee_percent']) {
                $productTypeServiceFee = $productType['pt_service_fee_percent'];
            }

            $prQuote = ProductQuote::create($productQuoteDto, $productTypeServiceFee);

            if (!$prQuote->save()) {
                throw new \DomainException('Save quote error. Errors: ' . VarDumper::dumpAsString($prQuote->getErrors()));
            }

            $cruiseQuote = new CruiseQuote();
            $cruiseQuote->crq_hash_key = $hashKey;
            $cruiseQuote->crq_cruise_id = $cruise->crs_id;
            $cruiseQuote->crq_product_quote_id = $prQuote->pq_id;
            $cruiseQuote->crq_data_json = $quote;
            $cruiseQuote->crq_amount_per_person = $quote['cabin']['price'];
            $cruiseQuote->crq_currency = $currency;
            $cruiseQuote->crq_adults = $cruise->getAdults();
            $cruiseQuote->crq_children = $cruise->getChildren();
            $cruiseQuote->crq_amount = $cruiseQuote->crq_amount_per_person * ($cruiseQuote->crq_adults + $cruiseQuote->crq_children);
            $cruiseQuote->crq_system_mark_up = 0;
            $cruiseQuote->crq_agent_mark_up = 0;
            $cruiseQuote->crq_service_fee_percent = ProductTypePaymentMethodQuery::getDefaultPercentFeeByProductType($prQuote->pqProduct->pr_type_id) ?? (self::SERVICE_FEE * 100);
            if (!$cruiseQuote->save()) {
                throw new \DomainException('Save Cruise Quote error. Errors: ' . VarDumper::dumpAsString($cruiseQuote->getErrors()));
            }

            $serviceFeeSum = (($cruiseQuote->crq_amount + $cruiseQuote->crq_system_mark_up) * $cruiseQuote->crq_service_fee_percent / 100);
            $totalSystemPrice = $cruiseQuote->crq_amount + $serviceFeeSum + $cruiseQuote->crq_system_mark_up;
            $systemPrice = ProductQuoteHelper::calcSystemPrice((float)$totalSystemPrice, $prQuote->pq_origin_currency);
            $prQuote->setQuotePrice(
                (float)$cruiseQuote->crq_amount,
                (float)$systemPrice,
                ProductQuoteHelper::roundPrice($systemPrice * $prQuote->pq_client_currency_rate),
                $serviceFeeSum
            );
            $prQuote->recalculateProfitAmount();
            $prQuote->save();

            $transaction->commit();

            return $cruiseQuote->crq_id;
        } catch (\Throwable $e) {
            $transaction->rollBack();
            throw $e;
        }
    }

    private function getHash(string $str): string
    {
        return $str;
    }

    public static function generateGid(): string
    {
        return md5(uniqid('crq', true));
    }
}

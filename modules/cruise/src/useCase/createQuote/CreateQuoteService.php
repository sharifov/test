<?php

namespace modules\cruise\src\useCase\createQuote;

use modules\cruise\src\entity\cruise\Cruise;
use modules\cruise\src\entity\cruiseQuote\CruiseQuote;
use modules\product\src\entities\productQuote\ProductQuote;
use modules\product\src\entities\productQuote\ProductQuoteStatus;
use sales\helpers\product\ProductQuoteHelper;
use yii\helpers\VarDumper;

class CreateQuoteService
{
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
            $prQuote = new ProductQuote();
            $prQuote->pq_product_id = $cruise->crs_product_id;
            $prQuote->pq_origin_currency = $currency;
            $prQuote->pq_client_currency = ProductQuoteHelper::getClientCurrencyCode($cruise->product);
            $prQuote->pq_owner_user_id = $userId;
            $prQuote->pq_price = (float)$totalAmount;
            $prQuote->pq_origin_price = (float)$totalAmount;
            $prQuote->pq_client_price = (float)$totalAmount;
            $prQuote->pq_status_id = ProductQuoteStatus::PENDING;
            $prQuote->pq_gid = self::generateGid();
            $prQuote->pq_service_fee_sum = 0;
            $prQuote->pq_client_currency_rate = ProductQuoteHelper::getClientCurrencyRate($cruise->product);
            $prQuote->pq_origin_currency_rate = 1;
            $prQuote->pq_name = $quote['cabin']['code'];
            if (!$prQuote->save()) {
                throw new \DomainException('Save quote error. Errors: ' . VarDumper::dumpAsString($prQuote->getErrors()));
            }

            $cruiseQuote = new CruiseQuote();
            $cruiseQuote->crq_hash_key = $hashKey;
            $cruiseQuote->crq_cruise_id = $cruise->crs_id;
            $cruiseQuote->crq_product_quote_id = $prQuote->pq_id;
            $cruiseQuote->crq_data_json = $quote;
            if (!$cruiseQuote->save()) {
                throw new \DomainException('Save Cruise Quote error. Errors: ' . VarDumper::dumpAsString($cruiseQuote->getErrors()));
            }

            $totalSystemPrice = $prQuote->pq_price * ($cruise->getAdults() + $cruise->getChildren());
            $systemPrice = ProductQuoteHelper::calcSystemPrice((float)$totalSystemPrice, $prQuote->pq_origin_currency);
            $prQuote->setQuotePrice(
                (float)$totalAmount,
                (float)$systemPrice,
                ProductQuoteHelper::roundPrice($systemPrice * $prQuote->pq_client_currency_rate),
                0
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

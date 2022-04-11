<?php

namespace src\behaviors\quotePrice;

use common\models\Quote;
use common\models\QuotePrice;
use src\helpers\app\AppHelper;
use src\services\CurrencyHelper;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class ClientPriceBehavior
 */
class ClientPriceBehavior extends \yii\base\Behavior
{
    /**
     * @return array
     */
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'clientPriceFill',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'clientPriceFill',
        ];
    }

    public function clientPriceFill(): void
    {
        try {
            if (!$this->owner instanceof QuotePrice) {
                throw new \RuntimeException('Owner class must by instanceof "QuotePrice"');
            }
            if (!$this->owner->quote instanceof Quote) {
                throw new \RuntimeException('Class must by instanceof "Quote"');
            }

            if ($this->owner->quote->isClientCurrencyDefault()) {
                $this->owner->qp_client_net = $this->owner->qp_client_net ?: $this->owner->net;
                $this->owner->qp_client_fare = $this->owner->qp_client_fare ?: $this->owner->fare;
                $this->owner->qp_client_taxes = $this->owner->qp_client_taxes ?: $this->owner->taxes;
                $this->owner->qp_client_markup = $this->owner->qp_client_markup ?: $this->owner->mark_up;
                $this->owner->qp_client_selling = $this->owner->qp_client_selling ?: $this->owner->selling;
                $this->owner->qp_client_service_fee = $this->owner->qp_client_service_fee ?: $this->owner->service_fee;
                $this->owner->qp_client_extra_mark_up = $this->owner->qp_client_extra_mark_up ?: $this->owner->extra_mark_up;
            } else {
                $rate = $this->owner->quote->q_client_currency_rate;
                $this->owner->qp_client_net = $this->owner->qp_client_net ?:
                    CurrencyHelper::convertFromBaseCurrency($this->owner->net, $rate);
                $this->owner->qp_client_fare = $this->owner->qp_client_fare ?:
                    CurrencyHelper::convertFromBaseCurrency($this->owner->fare, $rate);
                $this->owner->qp_client_taxes = $this->owner->qp_client_taxes ?:
                    CurrencyHelper::convertFromBaseCurrency($this->owner->taxes, $rate);
                $this->owner->qp_client_markup = $this->owner->qp_client_markup ?:
                    CurrencyHelper::convertFromBaseCurrency($this->owner->mark_up, $rate);
                $this->owner->qp_client_selling = $this->owner->qp_client_selling ?:
                    CurrencyHelper::convertFromBaseCurrency($this->owner->selling, $rate);
                $this->owner->qp_client_service_fee = $this->owner->qp_client_service_fee ?:
                    CurrencyHelper::convertFromBaseCurrency($this->owner->service_fee, $rate);
                $this->owner->qp_client_extra_mark_up = $this->owner->qp_client_extra_mark_up ?:
                    CurrencyHelper::convertFromBaseCurrency($this->owner->extra_mark_up, $rate);
            }
        } catch (\RuntimeException | \DomainException $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                'model' => ArrayHelper::toArray($this->owner)
            ]);
            \Yii::warning($message, 'ClientPriceBehavior:clientPriceDefault:Exception');
        } catch (\Throwable $throwable) {
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                'model' => ArrayHelper::toArray($this->owner)
            ]);
            \Yii::error($message, 'ClientPriceBehavior:clientPriceDefault:Throwable');
        }
    }
}

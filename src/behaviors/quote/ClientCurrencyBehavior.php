<?php

namespace src\behaviors\quote;

use common\models\Currency;
use common\models\Quote;
use src\helpers\app\AppHelper;
use yii\db\ActiveRecord;
use yii\helpers\ArrayHelper;

/**
 * Class ClientCurrencyBehavior
 */
class ClientCurrencyBehavior extends \yii\base\Behavior
{
    public function events(): array
    {
        return [
            ActiveRecord::EVENT_BEFORE_INSERT => 'clientCurrencyFill',
            ActiveRecord::EVENT_BEFORE_UPDATE => 'clientCurrencyFill',
        ];
    }

    public function clientCurrencyFill(): void
    {
        try {
            if (!$this->owner instanceof Quote) {
                throw new \RuntimeException('Owner class must by instanceof "Quote"');
            }
            if (empty($this->owner->q_client_currency)) {
                if ($prefCurrency = $this->owner->lead->leadPreferences->prefCurrency ?? null) {
                    $this->owner->q_client_currency = $prefCurrency->cur_code;
                    $this->owner->q_client_currency_rate = $prefCurrency->cur_base_rate;
                } else {
                    $this->owner->q_client_currency = Currency::getDefaultCurrencyCode();
                    $this->owner->q_client_currency_rate = Currency::getDefaultBaseCurrencyRate();
                }
            }
        } catch (\Throwable $throwable) {
            if (empty($this->owner->q_client_currency)) {
                $this->owner->q_client_currency = Currency::getDefaultCurrencyCode();
                $this->owner->q_client_currency_rate = Currency::getDefaultBaseCurrencyRate();
            }
            $message = ArrayHelper::merge(AppHelper::throwableLog($throwable), [
                'model' => ArrayHelper::toArray($this->owner)
            ]);
            \Yii::error($message, 'ClientCurrencyBehavior:clientCurrencyFill:Throwable');
        }
    }
}

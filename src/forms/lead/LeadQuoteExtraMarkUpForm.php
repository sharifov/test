<?php

namespace src\forms\lead;

use common\models\Currency;
use yii\base\Model;

class LeadQuoteExtraMarkUpForm extends Model
{
    /**
     * @var float
     */
    public $extra_mark_up;

    /**
     * @var float
     */
    public $qp_client_extra_mark_up;

    public $clientCurrencyCode;

    public $clientCurrencyRate;


    public function __construct(string $clientCurrencyCode, float $clientCurrencyRate, $config = [])
    {
        $this->clientCurrencyCode = $clientCurrencyCode;
        $this->clientCurrencyRate = $clientCurrencyRate;

        parent::__construct($config);
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['extra_mark_up'], 'number','min' => 0 ],
            [['qp_client_extra_mark_up'], 'number','min' => 0],
            [['qp_client_extra_mark_up'], 'validateCurrencyRate'],
        ];
    }

    public function validateCurrencyRate($attribute, $value)
    {
        if (round($this->extra_mark_up, 4) !== round($this->qp_client_extra_mark_up * $this->clientCurrencyRate, 4)) {
            return false;
        }
        return true;
    }





    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'extra_mark_up' => 'Default Currency Extra Mark-Up (' . Currency::getDefaultCurrencyCode() . ')',
            'qp_client_extra_mark_up' => 'Client Currency Extra Mark-Up (' . $this->clientCurrencyCode . ')',
        ];
    }

    public function formName()
    {
        return '';
    }
}

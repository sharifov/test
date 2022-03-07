<?php

namespace src\forms\lead;

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


    public $clientCurrencyRate;


    public function __construct(float $clientCurrencyRate, $config = [])
    {
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
            [['qp_client_extra_mark_up'], 'validateCurrencyRate', 'message' => 'Incorrect client/default mark-up relation'],
        ];
    }

    public function validateCurrencyRate($attribute, $value)
    {
        if (round($this->extra_mark_up, 2) !== round($this->qp_client_extra_mark_up * $this->clientCurrencyRate, 2)) {
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
            'extra_mark_up' => 'Default Currency Extra Mark-Up ',
            'qp_client_extra_mark_up' => 'Client Currency Extra Mark-Up',
        ];
    }

    public function formName()
    {
        return '';
    }
}

<?php

namespace src\forms\lead;

use yii\base\Model;

class LeadQuoteExtraMarkUpForm extends Model
{
    private const MAX_EXTRA_MARK_UP_FACTOR = 10;
    /**
     * @var float
     */
    public $extra_mark_up;

    /**
     * @var float
     */
    public $qp_client_extra_mark_up;


    public $clientCurrencyRate;

    private $maxValueExtraMarkUp;


    public function __construct(float $clientCurrencyRate, ?float $maxValueExtraMarkUp, $config = [])
    {
        $this->clientCurrencyRate = $clientCurrencyRate;
        $this->maxValueExtraMarkUp = $maxValueExtraMarkUp ? ($maxValueExtraMarkUp * self::MAX_EXTRA_MARK_UP_FACTOR) : 0;

        parent::__construct($config);
    }


    /**
     * @return array
     */
    public function rules(): array
    {
        return [
            [['extra_mark_up'], 'number', 'min' => 0],
            [['qp_client_extra_mark_up'], 'number', 'min' => 0],
            [['qp_client_extra_mark_up'], 'validateCurrencyRate', 'message' => 'Incorrect client/default mark-up relation'],
            ['extra_mark_up', 'validateExtraMarkUp'],
        ];
    }

    public function validateCurrencyRate($attribute, $value)
    {
        if (round($this->extra_mark_up, 2) !== round($this->qp_client_extra_mark_up * $this->clientCurrencyRate, 2)) {
            return false;
        }
        return true;
    }

    public function validateExtraMarkUp($attribute, $value): bool
    {
        if (!empty($this->maxValueExtraMarkUp) && round($this->extra_mark_up, 2) > $this->maxValueExtraMarkUp) {
            $this->addError($attribute, 'Extra Mark Up more than Net Price');
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

    public function getMaxExtraMarkUp()
    {
        return $this->maxValueExtraMarkUp;
    }
}

<?php

namespace src\forms\lead;

use modules\featureFlag\FFlag;
use yii\base\Model;

class LeadQuoteExtraMarkUpForm extends Model
{
    private const MAX_EXTRA_MARK_UP_FACTOR = 10;
    private const ALLOWABLE_RATE = 0.05;

    public $extra_mark_up;
    public $qp_client_extra_mark_up;
    public $clientCurrencyRate;

    private $maxValueExtraMarkUp;

    public function __construct(float $clientCurrencyRate, ?float $maxValueExtraMarkUp, $config = [])
    {
        $this->clientCurrencyRate = $clientCurrencyRate;
        $this->maxValueExtraMarkUp = $maxValueExtraMarkUp ? ($maxValueExtraMarkUp * self::MAX_EXTRA_MARK_UP_FACTOR) : 0;

        parent::__construct($config);
    }

    public function rules(): array
    {
        return [
            [['extra_mark_up'], 'number', 'min' => 0],
            [['extra_mark_up'], 'default', 'value' => 0.00],
            [['extra_mark_up'], 'validateExtraMarkUp'],

            [['qp_client_extra_mark_up'], 'number', 'min' => 0],
            [['qp_client_extra_mark_up'], 'default', 'value' => 0.00],
            [['qp_client_extra_mark_up'], 'validateCurrencyRate', 'message' => 'Incorrect client/default mark-up relation'],
        ];
    }

    public function validateCurrencyRate($attribute, $value): void
    {
        /** @fflag FFlag::FF_KEY_VALIDATE_CHANGE_EXTRA_MARK_UP, Enable validate change Extra Mark Up in lead/view page */
        if (\Yii::$app->featureFlag->isEnable(FFlag::FF_KEY_VALIDATE_CHANGE_EXTRA_MARK_UP)) {
            $diffAbs = abs($this->extra_mark_up - ($this->qp_client_extra_mark_up / $this->clientCurrencyRate));

            if ($diffAbs > self::ALLOWABLE_RATE) {
                $this->addError($attribute, 'Extra Mark Up not equal Client Extra Mark-Up');
                $message = [
                    'message' => 'Extra Mark Up not equal Client Extra Mark-Up',
                    'extraMarkUp' => $this->extra_mark_up,
                    'clientExtraMarkUp' => $this->qp_client_extra_mark_up,
                    'clientCurrencyRate' => $this->clientCurrencyRate,
                    'diffAbs' => $diffAbs,
                    'allowableRate' => self::ALLOWABLE_RATE,
                ];
                \Yii::warning($message, 'LeadQuoteExtraMarkUpForm:validateCurrencyRate');
            }
        }
    }

    public function validateExtraMarkUp($attribute, $value): void
    {
        if (!empty($this->maxValueExtraMarkUp) && round($this->extra_mark_up, 2) > $this->maxValueExtraMarkUp) {
            $this->addError($attribute, 'Extra Mark Up more than Net Price');
        }
    }

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

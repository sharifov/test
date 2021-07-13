<?php

namespace sales\model\coupon\useCase\apiCreate;

use yii\base\Model;

/**
 * Class CouponCreateForm
 * @property $currencyCode
 * @property $amount
 * @property $percent
 * @property $startDate
 * @property $expirationDate
 * @property $reusableCount
 * @property $public
 */
class CouponCreateForm extends Model
{
    public $currencyCode;
    public $amount;
    public $percent;
    public $startDate;
    public $expirationDate;
    public $reusableCount;
    public $public = false;

    public function rules(): array
    {
        return [
            ['currencyCode', 'required'],
            ['currencyCode', 'in', 'range' => array_keys(\common\models\Currency::getList())],

            ['amount', 'required', 'when' => static function ($model) {
                return empty($model->percent);
            }],
            ['amount', 'integer'],
            ['amount', 'filter', 'filter' => 'intval'],

            ['percent', 'required', 'when' => static function ($model) {
                return empty($model->amount);
            }],
            ['percent', 'integer'],
            ['percent', 'filter', 'filter' => 'intval'],

            [['startDate', 'expirationDate'], 'date', 'format' => 'php:Y-m-d'],

            [['startDate'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d 00:00:00', strtotime($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            [['expirationDate'], 'filter', 'filter' => static function ($value) {
                return date('Y-m-d 23:59:59', strtotime($value));
            }, 'skipOnEmpty' => true, 'skipOnError' => true],

            [['expirationDate'], 'checkDate'],

            ['reusableCount', 'integer', 'min' => 1],
            ['reusableCount', 'default', 'value' => 1],
            ['reusableCount', 'filter', 'filter' => 'intval'],

            ['public', 'boolean'],
            ['public', 'default', 'value' => false],
            ['public', 'filter', 'filter' => 'boolval'],
        ];
    }

    public function checkDate($attribute): void
    {
        if ($this->startDate && $this->expirationDate && ($this->startDate > $this->expirationDate)) {
            $this->addError($attribute, 'expirationDate must be older startDate');
        }
    }

    public function formName(): string
    {
        return '';
    }

    public function getAmountCurrencyCode(): ?string
    {
        if ($this->amount && $this->currencyCode) {
            return $this->currencyCode . $this->amount;
        }
        return null;
    }
}

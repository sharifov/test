<?php

namespace modules\order\src\forms\api\createC2b;

use common\models\CreditCard;
use yii\base\Model;

/**
 * Class CreditCardForm
 * @package modules\order\src\forms\api
 */
class CreditCardForm extends Model
{
    public $number;
    public $holder_name;
    public $expiration_month;
    public $expiration_year;
    public $expiration;
    public $cvv;
    public $type;
    public $type_id;

    private const FORMAT_YEAR = [
        2 => 'y',
        4 => 'Y'
    ];

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['number', 'expiration', 'cvv'], 'required'],
            [['expiration_month', 'expiration_year'], 'integer'],
            [['number'], 'string', 'max' => 20],
            ['number', 'filter', 'filter' => static function ($value) {
                return str_replace(' ', '', $value);
            }],
            [['expiration'], 'string', 'max' => 18],
            [['expiration'], 'validateDateFormat'],
            [['expiration'], 'parseDate'],
            [['holder_name'], 'string', 'max' => 50],
            [['cvv'], 'string', 'max' => 4],
            [['type'], 'filter', 'filter' => function () {
                $this->type_id = array_search($this->type, CreditCard::TYPE_LIST, false) ?: null;
            }]
//            ['type', 'validateType']
        ];
    }

    public function validateDateFormat(): void
    {
        $fPattern = '/^\d{2}\/\d{2,4}$/';
        $sPattern = '/^\d{2} \/ \d{2,4}$/';
        if (!preg_match($fPattern, trim($this->expiration)) && !preg_match($sPattern, trim($this->expiration))) {
            $this->addError('expiration', 'The format of Expiration is invalid');
        }
    }

    public function parseDate()
    {
        $dateArr = explode('/', $this->expiration);

        if (!$dateArr) {
            $this->addError('expiration', 'Incorrect expire date');
        }

        if (isset($dateArr[0])) {
            $this->expiration_month = (int) trim($dateArr[0]);
        } else {
            $this->addError('expiration', 'Incorrect expire month');
        }

        $year = trim($dateArr[1] ?? '');
        $formatYear = self::FORMAT_YEAR[mb_strlen($year)] ?? null;
        if ($formatYear) {
            $date = \DateTime::createFromFormat($formatYear, $year);
            $this->expiration_year = (int) $date->format('Y');
        } else {
            $this->addError('expiration', 'Incorrect expire year');
        }
    }

    public function validateType()
    {
        $this->type_id = array_search($this->type, CreditCard::TYPE_LIST, false);
        if ((bool)$this->type_id === false) {
            $this->addError('type', 'Unknown type');
        }
    }

    public function formName(): string
    {
        return 'creditCard';
    }
}

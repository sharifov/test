<?php

namespace frontend\models\form;

use yii\base\Model;

/**
 * This is the model Form class for table "credit_card".
 *
 * @property int $cc_id
 * @property string $cc_number
 * @property string|null $cc_holder_name
 * @property int $cc_expiration_month
 * @property int $cc_expiration_year
 * @property string $cc_expiration
 * @property string|null $cc_cvv
 * @property int|null $cc_type_id
 * @property int|null $cc_status_id
 * @property bool|null $cc_is_expired
 *
 */
class CreditCardForm extends Model
{
    public $cc_id;
    public $cc_number;
    public $cc_holder_name;
    public $cc_expiration_month;
    public $cc_expiration_year;
    public $cc_expiration;
    public $cc_cvv;
    public $cc_type_id;
    public $cc_status_id;
    public $cc_is_expired;

    /**
     * @return string
     */
    public static function tableName(): string
    {
        return 'credit_card';
    }

    /**
     * @return array
     */
    public function rules()
    {
        return [
            [['cc_number', 'cc_expiration'], 'required'],
            [['cc_expiration_month', 'cc_expiration_year', 'cc_type_id', 'cc_status_id', 'cc_is_expired'], 'integer'],
            [['cc_number'], 'string', 'max' => 20],
            ['cc_number', 'filter', 'filter' => static function ($value) {
                return str_replace(' ', '', $value);
            }],
            //[['cc_expiration'], 'string', 'max' => 18],
            [['cc_expiration'], 'date', 'format' => 'MM / yyyy'],
            [['cc_expiration'], 'parseDate'],
            [['cc_holder_name'], 'string', 'max' => 50],
            [['cc_cvv'], 'string', 'max' => 4],
        ];
    }

    public function parseDate()
    {
        $dateArr = explode('/', $this->cc_expiration);

        if (!$dateArr) {
            $this->addError('cc_expiration', 'Incorrect expire date');
        }

        if (isset($dateArr[0])) {
            $this->cc_expiration_month = (int) trim($dateArr[0]);
        } else {
            $this->addError('cc_expiration', 'Incorrect expire month');
        }

        if (isset($dateArr[1])) {
            $this->cc_expiration_year = (int) '20' . trim($dateArr[1]);
        } else {
            $this->addError('cc_expiration', 'Incorrect expire year');
        }


    }

    /**
     * @return array
     */
    public function attributeLabels(): array
    {
        return [
            'cc_id' => 'ID',
            'cc_number' => 'Card Number',
            'cc_holder_name' => 'Holder Name',
            'cc_expiration_month' => 'Expiration Month',
            'cc_expiration_year' => 'Expiration Year',
            'cc_expiration' => 'Expiration MM/YY',
            'cc_cvv' => 'CVV',
            'cc_type_id' => 'Type',
            'cc_status_id' => 'Status',
            'cc_is_expired' => 'Is Expired',
        ];
    }
}

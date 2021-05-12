<?php

namespace modules\order\src\forms\api\createC2b;

use yii\base\Model;
use yii\validators\DateValidator;

/**
 * Class HotelRequestForm
 * @package modules\order\src\forms\api\createC2b
 *
 * @property string $destinationCode
 * @property string $destinationName
 * @property string $checkIn
 * @property string $checkOut
 */
class HotelRequestForm extends Model
{
    public $destinationCode;

    public $destinationName;

    public $checkIn;

    public $checkOut;

    public function rules()
    {
        return [
            [['destinationCode', 'checkIn', 'checkOut', 'destinationName'], 'string'],
            [['destinationCode', 'checkIn', 'checkOut', 'destinationName'], 'required'],
            [['checkOut', 'checkOut'], 'date', 'format' => 'Y-m-d'],
            [['checkOut', 'checkOut'], 'validateDate'],
        ];
    }

    public function formName()
    {
        return 'hotelRequest';
    }

    public function validateDate($attribute, $params, $validator, $value)
    {
        $d = \DateTime::createFromFormat('Y-m-d', $value);
        if (!$d || $d->format('Y-m-d') !== $value) {
            $this->addError($attribute, 'Date is invalid');
            return false;
        }
    }
}

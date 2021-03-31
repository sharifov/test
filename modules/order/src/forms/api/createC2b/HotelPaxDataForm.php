<?php

namespace modules\order\src\forms\api\createC2b;

/**
 * Class HotelPaxDataForm
 * @package modules\order\src\forms\api\createC2b
 *
 * @property string $type
 * @property string $hotelRoomKey
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $birth_date
 * @property int|null $age
 */
class HotelPaxDataForm extends \yii\base\Model
{
    public $type;

    public $first_name;

    public $last_name;

    public $birth_date;

    public $hotelRoomKey;

    public $age;

    public function rules(): array
    {
        return [
            [['type', 'hotelRoomKey', 'birth_date'], 'string'],
            [['age'], 'integer'],
            ['type', 'in', 'range' => ['ADT', 'CHD']],

            ['birth_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Birth date is invalid. Valid format is Y-m-d.'],

            [['first_name', 'last_name'], 'string', 'max' => 40],
        ];
    }

    public function formName()
    {
        return 'hotelPaxData';
    }
}

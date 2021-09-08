<?php

namespace modules\flight\src\useCases\sale\form;

use modules\flight\models\FlightPax;
use yii\base\Model;

/**
 * Class FlightPaxForm
 *
 * @property $first_name
 * @property $middle_name
 * @property $last_name
 * @property $birth_date
 * @property $gender
 * @property $ticket_number
 * @property $type
 */
class FlightPaxForm extends Model
{
    public $first_name;
    public $middle_name;
    public $last_name;
    public $birth_date;
    public $gender;
    public $ticket_number;
    public $type;
    public $email;

    public function rules(): array
    {
        return [
            [['type'], 'required'],
            [['type'], 'string', 'max' => 3],
            [['type'], 'in', 'range' => array_keys(FlightPax::PAX_LIST_ID)],

            [['first_name', 'middle_name', 'last_name'], 'string', 'max' => 40],

            [['birth_date'], 'date', 'format' => 'php:Y-m-d'],

            [['gender'], 'string', 'max' => 1],

            [['ticket_number'], 'string', 'max' => 50],

            [['email'], 'string', 'max' => 100],
            [['email'], 'email', 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

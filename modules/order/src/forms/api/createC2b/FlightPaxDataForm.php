<?php

namespace modules\order\src\forms\api\createC2b;

use common\models\Language;
use modules\flight\models\FlightPax;

/**
 * Class PaxesForm
 * @package modules\order\src\forms\api\create
 *
 * @property string $type
 * @property string|null $first_name
 * @property string|null $last_name
 * @property string|null $middle_name
 * @property string|null $nationality
 * @property string|null $gender
 * @property string|null $birth_date
 * @property string|null $email
 * @property string|null $language
 * @property string|null $citizenship
 */
class FlightPaxDataForm extends \yii\base\Model
{
    public $type;

    public $first_name;

    public $last_name;

    public $middle_name;

    public $nationality;

    public $gender;

    public $birth_date;

    public $email;

    public $language;

    public $citizenship;

    public function rules(): array
    {
        return [
            [['type'], 'string'],
            ['type', 'in', 'range' => ['ADT', 'CHD', 'INF']],

            [['first_name', 'last_name', 'middle_name'], 'string', 'max' => 40],

            ['birth_date', 'date', 'format' => 'php:Y-m-d', 'message' => 'Birth date is invalid. Valid format is Y-m-d.'],

            [['gender'], 'string', 'max' => 1],
            [['nationality', 'language', 'citizenship'], 'string', 'max' => 5],

            [['language'], 'exist', 'targetClass' => Language::class, 'targetAttribute' => 'language_id', 'skipOnEmpty' => true],

            ['email', 'string', 'max' => 100],
            ['email', 'email']
        ];
    }

    public function formName()
    {
        return 'flightPaxData';
    }
}

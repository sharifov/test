<?php

namespace sales\forms\lead;

use yii\base\Model;

/**
 * Class ClientCreateForm
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 */
class ClientCreateForm extends Model
{

    public $firstName;
    public $middleName;
    public $lastName;

    public function rules(): array
    {
        return [

            ['firstName', 'required'],

            [['firstName', 'middleName', 'lastName'], 'string', 'max' => 100],

        ];
    }

}

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

	public $id;
    public $firstName;
    public $middleName;
    public $lastName;

    /**
     * @return array
     */
    public function rules(): array
    {
        return [

            ['firstName', 'required'],
			['id', 'integer'],
            [['firstName', 'middleName', 'lastName'], 'string', 'max' => 100],
            [['firstName', 'middleName', 'lastName'], 'match', 'pattern' => "/^[a-z-\s\']+$/i"],
            [['firstName', 'middleName', 'lastName'], 'filter', 'filter' => 'trim'],

        ];
    }

}

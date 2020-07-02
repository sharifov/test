<?php

namespace sales\forms\lead;

use yii\base\Model;

/**
 * Class ClientCreateForm
 * @property string $firstName
 * @property string $middleName
 * @property string $lastName
 * @property string $uuid
 */
class ClientCreateForm extends Model
{

	public $id;
    public $firstName;
    public $middleName;
    public $lastName;
    public $uuid;

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
			['uuid', 'string', 'max' => 36]
        ];
    }

}

<?php

namespace src\services\badges\form;

use common\components\validators\IsArrayValidator;
use src\services\badges\BadgesDictionary;
use yii\base\Model;

/**
 * Class BadgeForm
 */
class BadgeForm extends Model
{
    public $objectKey;
    public $idName;
    public $types;

    public function rules(): array
    {
        return [
            [['objectKey'], 'required'],
            [['objectKey'], 'string', 'max' => 50],
            [['objectKey'], 'in', 'range' => array_keys(BadgesDictionary::KEY_OBJECT_LIST)],

            [['idName'], 'required'],
            [['idName'], 'string', 'max' => 50],

            [['types'], IsArrayValidator::class],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

<?php

namespace src\forms\api;

use common\components\validators\IsNotArrayValidator;
use yii\base\Model;

/**
 * Class VisitorForm
 *
 * @property string $id
 * @property string $ipAddress
 * @property string $userAgent
 */
class VisitorForm extends Model
{
    public $id;
    public $ipAddress;
    public $userAgent;

    public function rules(): array
    {
        return [
            ['id', 'string', 'max' => '32'],
            ['id', IsNotArrayValidator::class],

            ['ipAddress', 'ip'],
            ['ipAddress', IsNotArrayValidator::class],

            ['userAgent', 'string', 'max' => 255],
            ['userAgent', IsNotArrayValidator::class],
        ];
    }

    public function formName(): string
    {
        return 'visitor';
    }
}

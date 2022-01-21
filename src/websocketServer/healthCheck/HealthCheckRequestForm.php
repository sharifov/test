<?php

namespace src\websocketServer\healthCheck;

use common\components\validators\AlphabetValidator;
use yii\base\Model;

/**
 * Class HealthCheckRequestForm
 *
 * @property $ping
 */
class HealthCheckRequestForm extends Model
{
    public $ping;

    public function rules(): array
    {
        return [
            ['ping', 'required'],
            ['ping', 'filter', 'filter' => 'trim'],
            ['ping', 'string', 'min' => 3, 'max' => 50],
            ['ping', AlphabetValidator::class],
        ];
    }

    public function formName(): string
    {
        return '';
    }
}

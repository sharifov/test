<?php

namespace console\socket\Commands\ChatSubscribe;

use yii\base\Model;
use common\components\validators\IsArrayValidator;

/**
 * Class Params
 *
 * @property $subscribe
 * @property $unSubscribe
 */
class Params extends Model
{
    public $subscribe;
    public $unSubscribe;

    public function rules(): array
    {
        return [
            [['subscribe', 'unSubscribe'], IsArrayValidator::class, 'skipOnEmpty' => false],
            [['subscribe', 'unSubscribe'], 'each', 'rule' => ['integer', 'skipOnEmpty' => false, 'skipOnError' => true], 'skipOnEmpty' => false, 'skipOnError' => true],
            [['subscribe', 'unSubscribe'], 'each', 'rule' => ['filter', 'filter' => 'intval', 'skipOnEmpty' => true], 'skipOnEmpty' => true, 'skipOnError' => true],
            [['subscribe', 'unSubscribe'], 'each', 'rule' => ['filter', 'filter' => static function ($value) {
                return 'chat-' . $value;
            }], 'skipOnEmpty' => true, 'skipOnError' => true],
        ];
    }

    public function isEmpty(): bool
    {
        return empty($this->subscribe) && empty($this->unSubscribe);
    }

    public function formName(): string
    {
        return '';
    }
}
